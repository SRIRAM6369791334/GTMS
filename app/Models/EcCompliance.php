<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcCompliance extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'ec_compliances';

    protected $fillable = [
        'compliance_no',
        'customer_id',
        'environment_project_id',
        'ec_certificate_id',
        'project_name',
        'district_id',
        'taluk_village',
        'mineral_id',
        'compliance_period',
        'compliance_year',
        'submission_due_date',
        'submission_date',
        'parivesh_app_no',
        'parivesh_acknowledgement_no',
        'parivesh_uploaded_date',
        'nabl_lab_name',
        'nabl_certificate_no',
        'monitoring_date',
        'status',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'payment_notes',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'submission_due_date'    => 'date',
        'submission_date'        => 'date',
        'parivesh_uploaded_date' => 'date',
        'monitoring_date'        => 'date',
        'product_value'          => 'decimal:2',
        'paid_amount'            => 'decimal:2',
        'pending_amount'         => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function environmentProject(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProject::class)->withTrashed();
    }

    public function ecCertificate(): BelongsTo
    {
        return $this->belongsTo(EcCertificate::class)->withTrashed();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EcComplianceDocument::class, 'ec_compliance_id');
    }

    public function handlers(): HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')
            ->where('application_type', 'ec_compliance')
            ->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ApplicationPayment::class, 'application_id')
            ->where('application_type', 'ec_compliance');
    }
}
