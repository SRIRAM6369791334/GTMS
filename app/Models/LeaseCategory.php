<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'land_type',
        'status',
        'is_custom',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'status'    => 'integer',
    ];

    public function leaseApplications(): HasMany
    {
        return $this->hasMany(LeaseApplication::class, 'category_id');
    }
}
