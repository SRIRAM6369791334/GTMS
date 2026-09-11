<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnvironmentProject extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'environment_projects';

    protected $fillable = [
        'project_code',
        'customer_id',
        'mining_application_id',
        'lease_application_id',
        'category',
        'project_name',
        'district_id',
        'location',
        'contact_name',
        'contact_phone',
        'contact_email',
        'public_hearing_date',
        'public_hearing_minutes_file',
        'status',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'public_hearing_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class);
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EnvironmentDocument::class);
    }

    public function ecCertificates(): HasMany
    {
        return $this->hasMany(EcCertificate::class);
    }

    public function pptApplications(): HasMany
    {
        return $this->hasMany(PptApplication::class);
    }
}
