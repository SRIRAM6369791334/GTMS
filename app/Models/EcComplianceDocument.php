<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcComplianceDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ec_compliance_documents';

    protected $fillable = [
        'ec_compliance_id',
        'folder_category',
        'document_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_mandatory',
        'is_custom',
        'status',
        'review_note',
        'uploaded_by',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_custom'    => 'boolean',
        'file_size'    => 'integer',
    ];

    public function compliance(): BelongsTo
    {
        return $this->belongsTo(EcCompliance::class, 'ec_compliance_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
