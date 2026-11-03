<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineMovement extends Model
{
    use HasFactory;

    protected $table = 'pipeline_movements';

    protected $fillable = [
        'master_project_id',
        'from_department',
        'to_department',
        'action_type',
        'priority',
        'mistake_category',
        'reason',
        'rectification_notes',
        'officer_id',
        'officer_name',
        'metadata',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // Relationships
    // ==========================================

    public function masterProject(): BelongsTo
    {
        return $this->belongsTo(MasterProject::class, 'master_project_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    // ==========================================
    // Helper Checks & Accessors
    // ==========================================

    public function isReturn(): bool
    {
        return $this->action_type === 'Returned_For_Correction';
    }

    public function isForward(): bool
    {
        return $this->action_type === 'Forward_Transfer';
    }

    public function isResubmission(): bool
    {
        return $this->action_type === 'Resubmitted_Fix';
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match (strtolower((string) $this->priority)) {
            'urgent' => 'badge-danger bg-danger text-white',
            'high'   => 'badge-warning bg-warning text-dark',
            'normal' => 'badge-info bg-info text-white',
            'low'    => 'badge-secondary bg-secondary text-white',
            default  => 'badge-light bg-light text-dark',
        };
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action_type) {
            'Forward_Transfer'        => 'badge-primary bg-primary text-white',
            'Returned_For_Correction' => 'badge-danger bg-danger text-white',
            'Resubmitted_Fix'         => 'badge-success bg-success text-white',
            default                   => 'badge-secondary bg-secondary text-white',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action_type) {
            'Forward_Transfer'        => 'Forwarded to Next Department',
            'Returned_For_Correction' => 'Returned for Correction',
            'Resubmitted_Fix'         => 'Resubmitted Rectification',
            default                   => str_replace('_', ' ', (string) $this->action_type),
        };
    }

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeForwardTransfers(Builder $query): Builder
    {
        return $query->where('action_type', 'Forward_Transfer');
    }

    public function scopeReturnedForCorrection(Builder $query): Builder
    {
        return $query->where('action_type', 'Returned_For_Correction');
    }

    public function scopeResubmissions(Builder $query): Builder
    {
        return $query->where('action_type', 'Resubmitted_Fix');
    }

    public function scopeForProject(Builder $query, int $masterProjectId): Builder
    {
        return $query->where('master_project_id', $masterProjectId);
    }
}
