<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mineral extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'default_unit',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function leaseApplications(): HasMany
    {
        return $this->hasMany(LeaseApplication::class);
    }

    public function miningApplications(): HasMany
    {
        return $this->hasMany(MiningApplication::class);
    }

    public function stockpiles(): HasMany
    {
        return $this->hasMany(MineralStockpile::class);
    }
}
