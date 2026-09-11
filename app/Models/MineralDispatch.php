<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MineralDispatch extends Model
{
    use HasFactory;

    protected $table = 'mineral_dispatches';

    protected $fillable = [
        'mineral_stockpile_id',
        'dispatch_date',
        'quantity',
        'vehicle_number',
        'driver_name',
        'destination',
        'seigniorage_fee_inr',
        'challan_no',
        'status',
        'created_by',
    ];

    protected $casts = [
        'dispatch_date' => 'datetime',
        'quantity' => 'decimal:2',
        'seigniorage_fee_inr' => 'decimal:2',
    ];

    public function stockpile(): BelongsTo
    {
        return $this->belongsTo(MineralStockpile::class, 'mineral_stockpile_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
