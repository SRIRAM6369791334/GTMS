<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiningBoundaryPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'mining_application_id',
        'pillar_id',
        'latitude',
        'longitude',
        'elevation',
        'remarks',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'elevation' => 'decimal:2',
    ];

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class);
    }
}
