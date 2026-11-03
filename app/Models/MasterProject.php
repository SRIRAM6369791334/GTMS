<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProject extends Model
{
    use HasFactory, SoftDeletes, BelongsToBranch;

    protected $table = 'master_projects';

    protected $fillable = [
        'master_application_id',
        'customer_id',
        'branch_id',
        'district_id',
        'taluk',
        'village',
        'survey_numbers',
        'area_extent_ha',
        'mineral_name',
        'lease_application_id',
        'dgps_survey_id',
        'drone_survey_id',
        'mining_application_id',
        'environment_project_id',
        'ppt_application_id',
        'current_stage',
        'pipeline_status',
        'created_by',
    ];

    protected $casts = [
        'survey_numbers' => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class, 'lease_application_id');
    }

    public function dgpsSurvey(): BelongsTo
    {
        return $this->belongsTo(DgpsSurvey::class, 'dgps_survey_id');
    }

    public function droneSurvey(): BelongsTo
    {
        return $this->belongsTo(DroneSurvey::class, 'drone_survey_id');
    }

    public function miningApplication(): BelongsTo
    {
        return $this->belongsTo(MiningApplication::class, 'mining_application_id');
    }

    public function environmentProject(): BelongsTo
    {
        return $this->belongsTo(EnvironmentProject::class, 'environment_project_id');
    }

    public function pptApplication(): BelongsTo
    {
        return $this->belongsTo(PptApplication::class, 'ppt_application_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PipelineMovement::class, 'master_project_id')->orderBy('created_at', 'desc');
    }

    public function latestMovement(): HasOne
    {
        return $this->hasOne(PipelineMovement::class, 'master_project_id')->latestOfMany();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==========================================
    // Static Helpers & Generators
    // ==========================================

    /**
     * Generate sequential collision-safe Universal Master Application ID.
     * E.g. GTMS-2026-0001
     */
    public static function generateMasterId(?int $year = null): string
    {
        $year = $year ?: (int) date('Y');
        $prefix = "GTMS-{$year}-";

        $maxProject = static::withoutGlobalScopes()
            ->withTrashed()
            ->where('master_application_id', 'like', "{$prefix}%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(master_application_id, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextSeq = 1;
        if ($maxProject && preg_match('/-(\d+)$/', $maxProject->master_application_id, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // State Check Helpers
    // ==========================================

    public function isActive(): bool
    {
        return !in_array($this->pipeline_status, ['completed', 'archived', 'cancelled'], true);
    }

    public function isReturnedForCorrection(): bool
    {
        return $this->pipeline_status === 'returned_for_correction';
    }

    // ==========================================
    // Accessors
    // ==========================================

    public function getStageLabelAttribute(): string
    {
        return match (strtolower((string) $this->current_stage)) {
            'lease'                   => 'Lease Application',
            'dgps', 'dgps_survey'     => 'DGPS Land Survey',
            'drone', 'drone_survey'   => 'Drone Photogrammetry Survey',
            'survey'                  => 'Land & Drone Survey',
            'mining', 'mining_plan'   => 'Mining Plan Formulation',
            'environment', 'ec'       => 'Environmental Clearance',
            'ppt'                     => 'SEAC PPT Presentation',
            'completed'               => 'Clearance Completed',
            default                   => ucfirst(str_replace('_', ' ', (string) $this->current_stage)),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->pipeline_status) {
            'returned_for_correction' => [
                'label' => 'Returned For Correction',
                'class' => 'badge-danger bg-danger text-white',
                'icon'  => 'bi-exclamation-triangle-fill',
            ],
            'in_progress' => [
                'label' => 'In Progress',
                'class' => 'badge-primary bg-primary text-white',
                'icon'  => 'bi-arrow-repeat',
            ],
            'completed' => [
                'label' => 'Completed',
                'class' => 'badge-success bg-success text-white',
                'icon'  => 'bi-check-circle-fill',
            ],
            'under_scrutiny' => [
                'label' => 'Under Scrutiny',
                'class' => 'badge-warning bg-warning text-dark',
                'icon'  => 'bi-hourglass-split',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', (string) $this->pipeline_status)),
                'class' => 'badge-secondary bg-secondary text-white',
                'icon'  => 'bi-info-circle',
            ],
        };
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('pipeline_status', ['completed', 'archived', 'cancelled']);
    }

    public function scopeReturnedForCorrection(Builder $query): Builder
    {
        return $query->where('pipeline_status', 'returned_for_correction');
    }

    public function scopeStage(Builder $query, string $stage): Builder
    {
        return $query->where('current_stage', $stage);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('master_application_id', 'like', "%{$term}%")
                ->orWhere('village', 'like', "%{$term}%")
                ->orWhere('taluk', 'like', "%{$term}%")
                ->orWhere('survey_numbers', 'like', "%{$term}%")
                ->orWhere('mineral_name', 'like', "%{$term}%")
                ->orWhereHas('customer', function ($cq) use ($term) {
                    $cq->where('customer_name', 'like', "%{$term}%")
                        ->orWhere('company_name', 'like', "%{$term}%")
                        ->orWhere('mobile_num', 'like', "%{$term}%");
                });
        });
    }
}
