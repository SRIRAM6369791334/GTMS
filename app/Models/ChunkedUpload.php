<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChunkedUpload extends Model
{
    use HasFactory;

    protected $table = 'chunked_uploads';

    protected $fillable = [
        'upload_token',
        'user_id',
        'file_name',
        'original_name',
        'mime_type',
        'total_size',
        'chunk_size',
        'total_chunks',
        'uploaded_chunks',
        'target_module',
        'reference_id',
        'file_path',
        'file_hash',
        'checksum_sha256',
        'status',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'total_size' => 'integer',
        'chunk_size' => 'integer',
        'total_chunks' => 'integer',
        'uploaded_chunks' => 'integer',
        'reference_id' => 'integer',
        'user_id' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Relationship to the uploaded parts.
     */
    public function parts(): HasMany
    {
        return $this->hasMany(ChunkedUploadPart::class, 'chunked_upload_id');
    }

    /**
     * Relationship to the user who initiated the upload.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Determine if upload is completely assembled and processed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if upload file has been assembled on disk.
     */
    public function isAssembled(): bool
    {
        return in_array($this->status, ['assembled', 'processing', 'completed'], true);
    }

    /**
     * Get the percentage of uploaded chunks (0.00 to 100.00).
     */
    public function getProgressPercent(): float
    {
        if ($this->total_chunks <= 0) {
            return 0.0;
        }

        return round(($this->uploaded_chunks / $this->total_chunks) * 100, 2);
    }

    /**
     * Calculate which chunk indices (0-based) are still missing.
     *
     * @return array<int>
     */
    public function getMissingChunkIndices(): array
    {
        if ($this->total_chunks <= 0) {
            return [];
        }

        $allExpectedIndices = range(0, $this->total_chunks - 1);
        $uploadedIndices = $this->parts()
            ->where('is_uploaded', true)
            ->pluck('chunk_index')
            ->toArray();

        return array_values(array_diff($allExpectedIndices, $uploadedIndices));
    }

    /**
     * Mark the upload as assembled.
     */
    public function markAsAssembled(string $filePath, string $checksum): void
    {
        $this->update([
            'file_path' => $filePath,
            'checksum_sha256' => $checksum,
            'status' => 'assembled',
            'error_message' => null,
        ]);
    }

    /**
     * Mark the upload as failed with an error message.
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }
}
