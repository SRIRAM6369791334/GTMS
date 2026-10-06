<?php

namespace App\Jobs;

use App\Models\ChunkedUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessUploadedMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @param  ChunkedUpload|int  $upload
     */
    public function __construct(public ChunkedUpload|int $upload)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $uploadRecord = $this->resolveUpload();
        if (!$uploadRecord) {
            Log::error("[ProcessUploadedMediaJob] Unable to resolve ChunkedUpload record.");
            return;
        }

        if (empty($uploadRecord->file_path)) {
            $uploadRecord->markAsFailed("No file_path assigned to assembled upload #{$uploadRecord->id}");
            return;
        }

        $fullPath = public_path($uploadRecord->file_path);
        if (!file_exists($fullPath)) {
            $uploadRecord->markAsFailed("Assembled file missing on disk: {$fullPath}");
            return;
        }

        // 1. Verify MIME Magic Bytes via finfo
        $detectedMime = null;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detectedMime = finfo_file($finfo, $fullPath);
                finfo_close($finfo);
            }
        }

        if ($detectedMime) {
            $uploadRecord->mime_type = $detectedMime;
        }

        // 2. Dispatch Video Thumbnail Generation if media is video
        $isVideo = ($detectedMime && str_starts_with($detectedMime, 'video/'))
            || str_starts_with($uploadRecord->mime_type, 'video/')
            || in_array(strtolower(pathinfo($uploadRecord->file_name, PATHINFO_EXTENSION)), ['mp4', 'mov', 'avi', 'mkv', 'webm'], true);

        if ($isVideo) {
            try {
                GenerateVideoThumbnailJob::dispatch($uploadRecord);
            } catch (\Throwable $e) {
                Log::warning("[ProcessUploadedMediaJob] Failed to dispatch GenerateVideoThumbnailJob: " . $e->getMessage());
            }
        }

        // 3. Dispatch SHA-256 Checksum Verification Job
        try {
            VerifyFileChecksumJob::dispatch($uploadRecord);
        } catch (\Throwable $e) {
            Log::warning("[ProcessUploadedMediaJob] Failed to dispatch VerifyFileChecksumJob: " . $e->getMessage());
        }

        // 4. Mark status as completed
        $uploadRecord->status = 'completed';
        $uploadRecord->save();

        Log::info("[ProcessUploadedMediaJob] Successfully processed upload #{$uploadRecord->id} (token: {$uploadRecord->upload_token})");
    }

    /**
     * Resolve ChunkedUpload model instance.
     */
    protected function resolveUpload(): ?ChunkedUpload
    {
        if ($this->upload instanceof ChunkedUpload) {
            return $this->upload->fresh() ?? $this->upload;
        }

        return ChunkedUpload::find($this->upload);
    }
}
