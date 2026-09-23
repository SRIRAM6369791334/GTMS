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
        'sub_category',
        'b1_stage',
        'ppt_stage_1_id',
        'ppt_stage_2_id',
        'project_name',
        'district_id',
        'location',
        'contact_name',
        'contact_phone',
        'contact_email',
        'public_hearing_date',
        'public_hearing_minutes_file',
        'status',
        'product_value',
        'paid_amount',
        'pending_amount',
        'payment_status',
        'branch_id',
        'created_by',
    ];

    /**
     * Returns human-readable sub category label.
     * B1/SC1 → 'Sub Category 1 (Site & Mining Documentation)'
     * B1/SC2 → 'Sub Category 2 (EIA & TNPCB Submission)'
     * B2     → null
     */
    public function getSubCategoryLabelAttribute(): ?string
    {
        if ($this->category === 'B1') {
            return match ($this->sub_category) {
                'SC1' => 'Sub Category 1 — Site & Mining Documentation',
                'SC2' => 'Sub Category 2 — EIA & TNPCB Submission',
                default => null,
            };
        }
        return null;
    }

    /**
     * Returns the folder names for this project based on category + sub_category.
     * Used to build dynamic folder tab views.
     */
    public function getFolderNamesAttribute(): array
    {
        if ($this->category === 'B1' && $this->sub_category === 'SC1') {
            return ['Documents', 'Report', 'GIS & Maps', 'Signed Reports', 'PARIVESH Acknowledgements'];
        }
        if ($this->category === 'B1' && $this->sub_category === 'SC2') {
            return [
                'Documents (ToR Letter)',
                'Baseline Study',
                'Draft (12 Chapters)',
                'TNPCB Draft Submission',
                'Final EIA Report',
                'Uploading File',
            ];
        }
        // B2
        return [
            'Documents',
            'Site Photographs',
            'Report',
            'GIS & Maps',
            'Signed Reports',
            'PARIVESH Acknowledgements',
        ];
    }

    /**
     * Returns the display badge label for category + sub_category.
     */
    public function getCategoryBadgeAttribute(): string
    {
        if ($this->category === 'B1') {
            $sc = $this->sub_category === 'SC2' ? 'SC2' : 'SC1';
            return "B1 · {$sc}";
        }
        return 'B2';
    }

    protected $casts = [
        'public_hearing_date' => 'date',
        'product_value'       => 'decimal:2',
        'paid_amount'         => 'decimal:2',
        'pending_amount'      => 'decimal:2',
    ];

    public function handlers(): HasMany
    {
        return $this->hasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'environment')->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'environment');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
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

    public function pptStage1(): BelongsTo
    {
        return $this->belongsTo(PptApplication::class, 'ppt_stage_1_id');
    }

    public function pptStage2(): BelongsTo
    {
        return $this->belongsTo(PptApplication::class, 'ppt_stage_2_id');
    }

    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'loggable')->latest();
    }
}
