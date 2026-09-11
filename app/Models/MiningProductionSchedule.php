<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiningProductionSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'mining_application_id',
        'year_number',
        'production_target',
        'waste_removal',
    ];

    protected $casts = [
        'year_number' => 'integer',
        'production_target' => 'decimal:2',
        'waste_removal' => 'decimal:2',
    ];

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class);
    }
}
