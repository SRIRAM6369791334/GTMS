<?php

namespace App\Jobs;

use App\Models\ChunkedUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyFileChecksumJob implements ShouldQueue
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
            Log::warning("[VerifyFileChecksumJob] No file_path on upload record #{$upload->id}");
            return;
        }

        $fullPath = public_path($upload->file_path);
        if (!file_exists($fullPath)) {
            Log::error("[VerifyFileChecksumJob] File not found at {$fullPath} for upload #{$upload->id}");
            $upload->update([
                'status' => 'failed',
                'error_message' => "Assembled file not found at: {$fullPath}",
            ]);
            return;
        }

        // Low-memory streaming SHA-256 calculation directly from disk buffers
        $computedSha256 = hash_file('sha256', $fullPath);
        $upload->checksum_sha256 = $computedSha256;

        if (!empty($upload->file_hash)) {
            if (strcasecmp($upload->file_hash, $computedSha256) === 0) {
                Log::info("[VerifyFileChecksumJob] SHA-256 verified successfully for upload #{$upload->id}: {$computedSha256}");
            } else {
                Log::error("[VerifyFileChecksumJob] SHA-256 mismatch for upload #{$upload->id}. Expected: {$upload->file_hash}, Computed: {$computedSha256}");
                $upload->status = 'failed';
                $upload->error_message = "SHA-256 checksum mismatch. Expected: {$upload->file_hash}, Computed: {$computedSha256}";
            }
        }

        $upload->save();
    }
}
