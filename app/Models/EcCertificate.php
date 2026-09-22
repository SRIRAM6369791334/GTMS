<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ec_certificates';

    protected $fillable = [
        'ec_ref_no',
        'environment_project_id',
        'customer_id',
        'lease_application_id',
        'parivesh_app_no',
        'applicant_name',
        'issue_date',
        'expiry_date',
        'validity_years',
        'communication_type',
        'certificate_file',
        'conditions_summary',
        'status',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'validity_years' => 'integer',
    ];

    public function environmentProject(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProject::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for querying certificates expiring within specified days (90, 60, 30 days).
     */
    public function scopeExpiringWithin(Builder $query, int $days = 90): Builder
    {
        return $query->where('status', 'active')
            ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }
}
