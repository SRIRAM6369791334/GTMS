<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseSurveyNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_application_id',
        'survey_no',
        'extent_ha',
        'classification',
        'pattadar_name',
    ];

    protected $casts = [
        'extent_ha' => 'decimal:4',
    ];

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }
}
