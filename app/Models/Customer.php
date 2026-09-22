<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'secondary_contact_person',
        'company_name',
        'mimas_no',
        'mimas_number',
        'mimas_status',
        'slug',
        'mobile_num',
        'secondary_mobile_num',
        'email',
        'district_id',
        'mineral_id',
        'pan',
        'aadhaar_no',
        'gstin',
        'area',
        'address',
        'status',
        'user_id',
        'created_by',
    ];

    protected $casts = [
        'area' => 'decimal:2',
        'status' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($customer) {
            if (empty($customer->slug)) {
                $name = $customer->company_name ?: $customer->customer_name;
                $base = Str::slug($name) ?: 'customer';
                $slug = $base;
                $i = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . (++$i);
                }
                $customer->slug = $slug;
            }
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('company_name') || $customer->isDirty('customer_name')) {
                $name = $customer->company_name ?: $customer->customer_name;
                $base = Str::slug($name) ?: 'customer';
                $slug = $base;
                $i = 1;
                while (static::withTrashed()->where('slug', $slug)->where('id', '!=', $customer->id)->exists()) {
                    $slug = "{$base}-" . (++$i);
                }
                $customer->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leaseApplications(): HasMany
    {
        return $this->hasMany(LeaseApplication::class);
    }

    public function miningApplications(): HasMany
    {
        return $this->hasMany(MiningApplication::class);
    }

    public function environmentProjects(): HasMany
    {
        return $this->hasMany(EnvironmentProject::class);
    }

    public function pptApplications(): HasMany
    {
        return $this->hasMany(PptApplication::class);
    }

    public function dgpsSurveys(): HasMany
    {
        return $this->hasMany(DgpsSurvey::class);
    }

    public function droneSurveys(): HasMany
    {
        return $this->hasMany(DroneSurvey::class);
    }

    public function stockpiles(): HasMany
    {
        return $this->hasMany(MineralStockpile::class, 'quarry_customer_id');
    }

    public function ecCertificates(): HasMany
    {
        return $this->hasMany(EcCertificate::class);
    }
}
