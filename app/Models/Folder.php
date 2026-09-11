<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'name',
        'sort_order',
        'status',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function documentFields(): HasMany
    {
        return $this->hasMany(DocumentField::class)->orderBy('sort_order');
    }

    public function leaseDocuments(): HasMany
    {
        return $this->hasMany(LeaseDocument::class);
    }

    public function miningDocuments(): HasMany
    {
        return $this->hasMany(MiningDocument::class);
    }

    public function environmentDocuments(): HasMany
    {
        return $this->hasMany(EnvironmentDocument::class);
    }

    public function pptDocuments(): HasMany
    {
        return $this->hasMany(PptDocument::class);
    }

    public function dgpsDocuments(): HasMany
    {
        return $this->hasMany(DgpsDocument::class);
    }

    public function droneDocuments(): HasMany
    {
        return $this->hasMany(DroneDocument::class);
    }
}
