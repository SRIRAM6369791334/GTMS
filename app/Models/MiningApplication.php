<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiningApplication extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $fillable = [
        'common_id',
        'application_no',
        'customer_id',
        'lease_application_id',
        'nature_of_work_id',
        'parent_plan_id',
        'applicant_type_id',
        'district_id',
        'mineral_id',
        'other_mineral_name',
        'plan_type_id',
        'taluk',
        'village',
        'survey_numbers_text',
        'area_extent_ha',
        'start_date',
        'end_date',
        'validity_years',
        'stage',
        'status',
        'rqp_name',
        'rqp_reg_no',
        'safety_distance_meters',
        'assigned_inspector_id',
        'approval_order_no',
        'approval_date',
        'approval_file',
        'kml_file_path',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'area_extent_ha' => 'decimal:2',
        'product_value' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
        'safety_distance_meters' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approval_date' => 'date',
        'validity_years' => 'integer',
    ];

    public function handlers(): HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'mining')->orderBy('sort_order');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function natureOfWork(): BelongsTo
    {
        return $this->belongsTo(NatureOfWork::class);
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function parentPlan(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class, 'parent_plan_id');
    }

    public function revisedPlans(): HasMany
    {
        return $this->hasMany(MiningApplication::class, 'parent_plan_id');
    }

    public function applicantType(): BelongsTo
    {
        return $this->belongsTo(ApplicantType::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mineral(): BelongsTo
    {
        return $this->belongsTo(Mineral::class);
    }

    public function minerals(): BelongsToMany
    {
        return $this->belongsToMany(Mineral::class, 'mining_application_minerals');
    }

    public function planType(): BelongsTo
    {
        return $this->belongsTo(PlanType::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_inspector_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function boundaryPoints(): HasMany
    {
        return $this->hasMany(MiningBoundaryPoint::class);
    }

    public function productionSchedules(): HasMany
    {
        return $this->hasMany(MiningProductionSchedule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MiningDocument::class);
    }

    public function environmentProjects(): HasMany
    {
        return $this->hasMany(EnvironmentProject::class);
    }
}
