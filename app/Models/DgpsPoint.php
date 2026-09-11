<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DgpsPoint extends Model
{
    use HasFactory;

    protected $table = 'dgps_points';

    protected $fillable = [
        'dgps_survey_id',
        'pillar_no',
        'latitude',
        'longitude',
        'elevation',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'elevation' => 'decimal:2',
    ];

    public function dgpsSurvey(): BelongsTo
    {
        return $this->belongsTo(DgpsSurvey::class);
    }
}
