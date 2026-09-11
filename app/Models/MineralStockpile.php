<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MineralStockpile extends Model
{
    use HasFactory, BelongsToBranch;

    protected $table = 'mineral_stockpiles';

    protected $fillable = [
        'quarry_customer_id',
        'lease_application_id',
        'mineral_id',
        'branch_id',
        'annual_permitted_quota',
        'current_stock_cbm',
        'total_dispatched_cbm',
        'unit',
        'status',
    ];

    protected $casts = [
        'annual_permitted_quota' => 'decimal:2',
        'current_stock_cbm' => 'decimal:2',
        'total_dispatched_cbm' => 'decimal:2',
        'status' => 'integer',
    ];

    public function quarryCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'quarry_customer_id');
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(MineralStockEntry::class);
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(MineralDispatch::class);
    }
}
