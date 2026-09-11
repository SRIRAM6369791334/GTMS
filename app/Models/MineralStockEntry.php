<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MineralStockEntry extends Model
{
    use HasFactory;

    protected $table = 'mineral_stock_entries';

    protected $fillable = [
        'mineral_stockpile_id',
        'entry_date',
        'quantity',
        'source_type',
        'verified_by',
        'remarks',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function stockpile(): BelongsTo
    {
        return $this->belongsTo(MineralStockpile::class, 'mineral_stockpile_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
