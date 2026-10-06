<?php

namespace App\Services;

use App\Jobs\ProcessUploadedMediaJob;
use App\Models\ChunkedUpload;
use App\Models\ChunkedUploadPart;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class ChunkedUploadService
{
    /**
     * Default chunk size in bytes (10MB).
     */
    public const DEFAULT_CHUNK_SIZE = 10485760;

    /**
     * Maximum supported file size (50GB).
     */
    public const MAX_TOTAL_SIZE = 53687091200;

    /**
     * Initialize a chunked upload session.
     *
     * @param  array<string, mixed>  $data
     * @param  int|null  $userId
     * @return ChunkedUpload
     */
    public function initUpload(array $data, ?int $userId = null): ChunkedUpload
    {
        $token = 'upl_' . Str::random(32);

        $originalName = $data['original_name'] ?? $data['file_name'] ?? 'upload.bin';
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = Str::slug($basename) . ($extension ? '.' . strtolower($extension) : '');

        $totalSize = (int) ($data['total_size'] ?? $data['file_size'] ?? 0);
        if ($totalSize <= 0) {
            throw new RuntimeException('Total file size must be greater than zero.');
        }

        if ($totalSize > self::MAX_TOTAL_SIZE) {
            throw new RuntimeException('File size exceeds the 50GB system upload limit.');
        }

        $chunkSize = (int) ($data['chunk_size'] ?? self::DEFAULT_CHUNK_SIZE);
        if ($chunkSize <= 0) {
            $chunkSize = self::DEFAULT_CHUNK_SIZE;
        }

        $totalChunks = (int) ($data['total_chunks'] ?? ceil($totalSize / $chunkSize));
        if ($totalChunks <= 0) {
            $totalChunks = 1;
        }

        $mimeType = $data['mime_type'] ?? 'application/octet-stream';
        $targetModule = $data['target_module'] ?? $data['module'] ?? null;
        $referenceId = isset($data['reference_id']) ? (int) $data['reference_id'] : (isset($data['module_record_id']) ? (int) $data['module_record_id'] : null);
        $clientHash = $data['file_hash'] ?? null;

        // Build metadata array from remaining attributes
        $metadata = $data['metadata'] ?? [];
        if (!is_array($metadata)) {
            $metadata = [];
        }
        if (isset($data['document_name'])) {
            $metadata['document_name'] = $data['document_name'];
        }
        if (isset($data['folder_id'])) {
            $metadata['folder_id'] = $data['folder_id'];
        }

        // Prepare isolated disk directory for chunks
        $chunkDir = $this->getChunkDirectory($token);
        if (!File::isDirectory($chunkDir)) {
            File::makeDirectory($chunkDir, 0775, true, true);
        }

        return ChunkedUpload::create([
            'upload_token' => $token,
            'user_id' => $userId,
            'file_name' => $safeName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'total_size' => $totalSize,
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks,
            'uploaded_chunks' => 0,
            'target_module' => $targetModule,
            'reference_id' => $referenceId,
            'file_hash' => $clientHash,
            'status' => 'pending',
            'metadata' => $metadata,
        ]);
    }

    /**
     * Save a single uploaded chunk to disk and update session progress.
     *
     * @param  string  $token
     * @param  int  $chunkIndex
     * @param  UploadedFile  $file
     * @param  string|null  $clientChunkHash
     * @return ChunkedUploadPart
     */
    public function saveChunk(
        string $token,
        int $chunkIndex,
        UploadedFile $file,
        ?string $clientChunkHash = null
    ): ChunkedUploadPart {
        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();

        if (in_array($upload->status, ['cancelled', 'failed'], true)) {
            throw new RuntimeException("Cannot upload chunk to a {$upload->status} upload session.");
        }

        $chunkDir = $this->getChunkDirectory($token);
        if (!File::isDirectory($chunkDir)) {
            File::makeDirectory($chunkDir, 0775, true, true);
        }

        $partFilename = "chunk_{$chunkIndex}.part";
        $targetPath = $chunkDir . DIRECTORY_SEPARATOR . $partFilename;

        // Move uploaded chunk into position
        $file->move($chunkDir, $partFilename);

        if (!file_exists($targetPath)) {
            throw new RuntimeException("Failed to persist chunk {$chunkIndex} to disk.");
        }

        $chunkSize = filesize($targetPath);
        $computedHash = hash_file('sha256', $targetPath);

        // Client SHA-256 verification if provided
        if ($clientChunkHash !== null && !empty($clientChunkHash)) {
            if (strcasecmp($clientChunkHash, $computedHash) !== 0) {
                @unlink($targetPath);
                throw new RuntimeException("Chunk {$chunkIndex} SHA-256 checksum mismatch. Expected: {$clientChunkHash}, got: {$computedHash}");
            }
        }

        $relativeChunkPath = "chunks/{$token}/{$partFilename}";

        $part = ChunkedUploadPart::updateOrCreate(
            [
                'chunked_upload_id' => $upload->id,
                'chunk_index' => $chunkIndex,
            ],
            [
                'chunk_size' => $chunkSize,
                'chunk_path' => $relativeChunkPath,
                'chunk_hash' => $computedHash,
                'is_uploaded' => true,
            ]
        );

        // Recalculate uploaded chunks count
        $uploadedCount = $upload->parts()->where('is_uploaded', true)->count();
        $upload->uploaded_chunks = $uploadedCount;

        if ($upload->status === 'pending') {
            $upload->status = 'uploading';
        }

        $upload->save();

        return $part;
    }

    /**
     * Query status and resume information for an upload session.
     *
     * @param  string  $token
     * @return array<string, mixed>
     */
    public function getStatus(string $token): array
    {
        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();

        $uploadedIndices = $upload->parts()
            ->where('is_uploaded', true)
            ->orderBy('chunk_index')
            ->pluck('chunk_index')
            ->toArray();

        $missingIndices = $upload->getMissingChunkIndices();

        return [
            'upload_token' => $upload->upload_token,
            'file_name' => $upload->file_name,
            'original_name' => $upload->original_name,
            'mime_type' => $upload->mime_type,
            'total_size' => (int) $upload->total_size,
            'chunk_size' => (int) $upload->chunk_size,
            'total_chunks' => (int) $upload->total_chunks,
            'uploaded_chunks_count' => count($uploadedIndices),
            'uploaded_chunks' => $uploadedIndices,
            'missing_chunks' => $missingIndices,
            'is_complete' => empty($missingIndices),
            'progress_percent' => $upload->getProgressPercent(),
            'status' => $upload->status,
            'file_path' => $upload->file_path,
            'checksum_sha256' => $upload->checksum_sha256,
        ];
    }

    /**
     * Assemble all uploaded chunks into the destination file via low-memory binary streaming (<64KB RAM).
     *
     * @param  string  $token
     * @param  string|null  $targetModule
     * @param  int|null  $referenceId
     * @return array<string, mixed>
     */
    public function assembleFile(string $token, ?string $targetModule = null, ?int $referenceId = null): array
    {
        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();

        // Idempotency: If already assembled or completed, return current state
        if (in_array($upload->status, ['assembled', 'processing', 'completed'], true) && $upload->file_path) {
            $fullPath = public_path($upload->file_path);
            return [
                'success' => true,
                'upload_token' => $upload->upload_token,
                'file_name' => $upload->file_name,
                'file_path' => $upload->file_path,
                'full_path' => $fullPath,
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : $upload->total_size,
                'checksum_sha256' => $upload->checksum_sha256,
                'status' => $upload->status,
                'job_dispatched' => true,
            ];
        }

        // Verify all chunks exist
        $missing = $upload->getMissingChunkIndices();
        if (!empty($missing)) {
            throw new RuntimeException('Cannot assemble file: ' . count($missing) . ' chunks are missing.');
        }

        $upload->update(['status' => 'assembled']);

        $module = $targetModule ?? $upload->target_module ?? 'general';
        $refId = $referenceId ?? $upload->reference_id;

        // Destination folder hierarchy: public/uploads/{module}/[REF-{refId}/]
        $subDir = 'uploads/' . trim($module, '/');
        if ($refId !== null) {
            $subDir .= '/REF-' . $refId;
        }

        $destDir = public_path($subDir);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0775, true, true);
        }

        $finalFileName = time() . '_' . Str::random(6) . '_' . $upload->file_name;
        $destPath = $destDir . DIRECTORY_SEPARATOR . $finalFileName;
        $relativeFilePath = $subDir . '/' . $finalFileName;

        // Open destination file in binary write mode
        $destStream = fopen($destPath, 'wb');
        if ($destStream === false) {
            $upload->markAsFailed("Unable to open destination stream: {$destPath}");
            throw new RuntimeException("Unable to open destination stream at: {$destPath}");
        }

        $chunkDir = $this->getChunkDirectory($token);

        try {
            // Concatenate each chunk in strict sequential index order
            for ($i = 0; $i < $upload->total_chunks; $i++) {
                $chunkFile = $chunkDir . DIRECTORY_SEPARATOR . "chunk_{$i}.part";

                if (!file_exists($chunkFile)) {
                    throw new RuntimeException("Missing chunk file on disk: chunk_{$i}.part");
                }

                $chunkStream = fopen($chunkFile, 'rb');
                if ($chunkStream === false) {
                    throw new RuntimeException("Unable to open chunk binary stream: {$chunkFile}");
                }

                // Low-memory kernel stream copy (<64KB RAM usage)
                stream_copy_to_stream($chunkStream, $destStream);
                fclose($chunkStream);

                // Immediate cleanup of chunk file to reclaim disk space on Synology NAS
                @unlink($chunkFile);
            }
        } catch (\Throwable $e) {
            fclose($destStream);
            @unlink($destPath);
            $upload->markAsFailed('Stream assembly failed: ' . $e->getMessage());
            throw $e;
        }

        fclose($destStream);

        // Remove temporary session chunk folder
        if (File::isDirectory($chunkDir)) {
            @File::deleteDirectory($chunkDir);
        }

        // Compute verified SHA-256 without memory buffering
        $verifiedChecksum = hash_file('sha256', $destPath);

        // If client provided an initial file hash, verify full-file integrity
        if (!empty($upload->file_hash)) {
            if (strcasecmp($upload->file_hash, $verifiedChecksum) !== 0) {
                $upload->markAsFailed("Full-file SHA-256 verification failed. Expected: {$upload->file_hash}, computed: {$verifiedChecksum}");
                throw new RuntimeException("Full-file SHA-256 mismatch. File integrity check failed.");
            }
        }

        // Update upload record
        $upload->file_path = $relativeFilePath;
        $upload->checksum_sha256 = $verifiedChecksum;
        if ($targetModule) {
            $upload->target_module = $targetModule;
        }
        if ($referenceId) {
            $upload->reference_id = $referenceId;
        }
        $upload->status = 'assembled';
        $upload->save();

        // Dispatch background processing job
        try {
            ProcessUploadedMediaJob::dispatch($upload);
            $upload->status = 'processing';
            $upload->save();
            $jobDispatched = true;
        } catch (\Throwable $e) {
            Log::warning("Failed to dispatch ProcessUploadedMediaJob for upload {$upload->upload_token}: " . $e->getMessage());
            $jobDispatched = false;
        }

        return [
            'success' => true,
            'upload_token' => $upload->upload_token,
            'file_name' => $upload->file_name,
            'file_path' => $relativeFilePath,
            'full_path' => $destPath,
            'file_size' => filesize($destPath),
            'checksum_sha256' => $verifiedChecksum,
            'status' => $upload->status,
            'job_dispatched' => $jobDispatched,
        ];
    }

    /**
     * Cancel an upload session and purge all associated chunks.
     *
     * @param  string  $token
     * @return bool
     */
    public function cancelUpload(string $token): bool
    {
        $upload = ChunkedUpload::where('upload_token', $token)->first();
        if (!$upload) {
            return false;
        }

        // Purge physical chunks from disk
        $chunkDir = $this->getChunkDirectory($token);
        if (File::isDirectory($chunkDir)) {
            File::deleteDirectory($chunkDir);
        }

        // Clean up parts
        $upload->parts()->delete();

        // Mark upload as cancelled
        $upload->update([
            'status' => 'cancelled',
            'error_message' => 'Upload cancelled by user or system.',
        ]);

        return true;
    }

    /**
     * Get the absolute path to the temporary chunk storage directory for a token.
     *
     * @param  string  $token
     * @return string
     */
    protected function getChunkDirectory(string $token): string
    {
        return storage_path('app/chunks/' . $token);
    }
}
