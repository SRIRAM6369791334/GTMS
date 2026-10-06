<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChunkedUploadPart extends Model
{
    use HasFactory;

    protected $table = 'chunked_upload_parts';

    protected $fillable = [
        'chunked_upload_id',
        'chunk_index',
        'chunk_size',
        'chunk_path',
        'chunk_hash',
        'is_uploaded',
    ];

    protected $casts = [
        'chunk_index' => 'integer',
        'chunk_size' => 'integer',
        'is_uploaded' => 'boolean',
    ];

    /**
     * Relationship to parent ChunkedUpload.
     */
    public function chunkedUpload(): BelongsTo
    {
        return $this->belongsTo(ChunkedUpload::class, 'chunked_upload_id');
    }
}
