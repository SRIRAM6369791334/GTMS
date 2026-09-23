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
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'created_by',
    ];

    protected $casts = [
        'issue_date'     => 'date',
        'expiry_date'    => 'date',
        'validity_years' => 'integer',
        'product_value'  => 'decimal:2',
        'paid_amount'    => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    public function handlers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'ec')->orderBy('sort_order');
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'ec');
    }

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
