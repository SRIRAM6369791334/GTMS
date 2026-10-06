<?php

namespace App\Jobs;

use App\Models\ChunkedUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GenerateVideoThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(public ChunkedUpload $upload)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $upload = $this->upload->fresh() ?? $this->upload;

        if (empty($upload->file_path)) {
            Log::warning("[GenerateVideoThumbnailJob] No file_path on upload record #{$upload->id}");
            return;
        }

        $fullPath = public_path($upload->file_path);
        if (!file_exists($fullPath)) {
            Log::warning("[GenerateVideoThumbnailJob] Video source not found at {$fullPath}");
            return;
        }

        $thumbSubdir = 'uploads/thumbnails';
        $thumbDir = public_path($thumbSubdir);
        if (!File::isDirectory($thumbDir)) {
            File::makeDirectory($thumbDir, 0775, true, true);
        }

        $thumbName = "thumb_{$upload->id}_" . time() . ".jpg";
        $thumbDest = $thumbDir . DIRECTORY_SEPARATOR . $thumbName;
        $relativeThumb = $thumbSubdir . '/' . $thumbName;

        $ffmpegPath = $this->detectFfmpegBinary();

        if ($ffmpegPath) {
            $cmd = sprintf(
                '%s -y -ss 00:00:05 -i %s -vframes 1 -q:v 2 -vf "scale=640:-1" %s 2>&1',
                escapeshellcmd($ffmpegPath),
                escapeshellarg($fullPath),
                escapeshellarg($thumbDest)
            );

            @exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && file_exists($thumbDest)) {
                $metadata = $upload->metadata ?? [];
                $metadata['thumbnail_path'] = $relativeThumb;
                $metadata['thumbnail_generated_at'] = now()->toISOString();
                $metadata['has_video_thumbnail'] = true;

                $upload->metadata = $metadata;
                $upload->save();

                Log::info("[GenerateVideoThumbnailJob] Video thumbnail generated for upload #{$upload->id}: {$relativeThumb}");
                return;
            }

            Log::warning("[GenerateVideoThumbnailJob] FFmpeg execution returned code {$returnCode} for upload #{$upload->id}. Falling back to placeholder.");
        } else {
            Log::info("[GenerateVideoThumbnailJob] FFmpeg binary not found on host. Gracefully configuring default video thumbnail placeholder.");
        }

        // Graceful fallback when FFmpeg is not installed
        $metadata = $upload->metadata ?? [];
        $metadata['thumbnail_path'] = 'assets/images/defaults/video-placeholder.png';
        $metadata['has_video_thumbnail'] = false;
        $metadata['thumbnail_note'] = 'FFmpeg not present; standard video placeholder used.';

        $upload->metadata = $metadata;
        $upload->save();
    }

    /**
     * Detect FFmpeg executable across Windows and Linux environments.
     *
     * @return string|null
     */
    protected function detectFfmpegBinary(): ?string
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            $out = @shell_exec('where ffmpeg 2>nul');
            if ($out) {
                $lines = array_filter(array_map('trim', explode("\n", $out)));
                if (!empty($lines)) {
                    return reset($lines);
                }
            }
        } else {
            $out = @shell_exec('which ffmpeg 2>/dev/null');
            if ($out && trim($out) !== '') {
                return trim($out);
            }
        }

        // Common fallback paths on Windows / Docker
        $commonPaths = [
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
        ];

        foreach ($commonPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
