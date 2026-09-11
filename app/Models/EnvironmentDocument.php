<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvironmentDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'environment_documents';

    protected $fillable = [
        'environment_project_id',
        'folder_id',
        'document_field_id',
        'document_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'review_note',
        'reviewed_by',
        'reviewed_at',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'reviewed_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    public function environmentProject(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProject::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function documentField(): BelongsTo
    {
        return $this->belongsTo(DocumentField::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
