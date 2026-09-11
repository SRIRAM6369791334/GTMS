<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DgpsDocument extends Model
{
    use HasFactory;

    protected $table = 'dgps_documents';

    protected $fillable = [
        'dgps_survey_id',
        'folder_id',
        'document_name',
        'file_path',
        'status',
    ];

    public function dgpsSurvey(): BelongsTo
    {
        return $this->belongsTo(DgpsSurvey::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
