<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DroneDocument extends Model
{
    use HasFactory;

    protected $table = 'drone_documents';

    protected $fillable = [
        'drone_survey_id',
        'folder_id',
        'document_name',
        'file_path',
        'file_size',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function droneSurvey(): BelongsTo
    {
        return $this->belongsTo(DroneSurvey::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
