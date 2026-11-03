<?php

namespace App\Services;

use App\Models\DgpsSurvey;
use App\Models\DroneSurvey;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\MasterProject;
use App\Models\MiningApplication;
use App\Models\Notification;
use App\Models\PipelineMovement;
use App\Models\PptApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MasterPipelineService
{
    /**
     * Pipeline stages canonical order ranking for forward validation.
     */
    protected array $stageOrder = [
        'lease'        => 1,
        'survey_dgps'  => 2,
        'survey_drone' => 2,
        'survey'       => 2,
        'mining'       => 3,
        'environment'  => 4,
        'ppt'          => 5,
        'completed'    => 6,
    ];

    /**
     * Generate sequential collision-safe Universal Master Application ID atomically.
     * Format: GTMS-YYYY-XXXX (e.g. GTMS-2026-0001)
     */
    public function generateMasterId(?int $year = null): string
    {
        $year = $year ?: (int) date('Y');
        $prefix = "GTMS-{$year}-";

        return DB::transaction(function () use ($prefix, $year) {
            $maxProject = MasterProject::withoutGlobalScopes()
                ->withTrashed()
                ->where('master_application_id', 'like', "{$prefix}%")
                ->orderByRaw("CAST(SUBSTRING_INDEX(master_application_id, '-', -1) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($maxProject && preg_match('/-(\d+)$/', $maxProject->master_application_id, $matches)) {
                $nextSeq = (int) $matches[1] + 1;
            }

            $candidate = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

            // Double-check collision existence guard
            while (MasterProject::withoutGlobalScopes()->withTrashed()->where('master_application_id', $candidate)->exists()) {
                $nextSeq++;
                $candidate = $prefix . str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }

            return $candidate;
        });
    }

    /**
     * Create or retrieve existing MasterProject associated with a LeaseApplication.
     * Automatically extracts parcel geometry, survey numbers, and initiates the pipeline.
     */
    public function createOrGetMasterProject(LeaseApplication $lease, ?User $officer = null): MasterProject
    {
        if ($lease->master_project_id) {
            $existing = MasterProject::find($lease->master_project_id);
            if ($existing) {
                return $existing;
            }
        }

        if ($lease->relationLoaded('masterProject') && $lease->masterProject) {
            return $lease->masterProject;
        }

        return DB::transaction(function () use ($lease, $officer) {
            // Re-read with lock to avoid race conditions
            $lockedLease = LeaseApplication::lockForUpdate()->find($lease->id);
            if ($lockedLease && $lockedLease->master_project_id) {
                $existing = MasterProject::find($lockedLease->master_project_id);
                if ($existing) {
                    return $existing;
                }
            }

            $masterId = $this->generateMasterId();

            // Extract survey numbers from relationship or direct attribute
            $surveyNumbers = '';
            if ($lease->surveyNumbers && $lease->surveyNumbers->isNotEmpty()) {
                $surveyNumbers = $lease->surveyNumbers->pluck('survey_no')->filter()->implode(', ');
            } elseif (!empty($lease->survey_numbers)) {
                $surveyNumbers = (string) $lease->survey_numbers;
            }

            // Extract mineral name
            $mineralName = null;
            if ($lease->mineral) {
                $mineralName = $lease->mineral->name;
            } elseif (!empty($lease->other_mineral_name)) {
                $mineralName = $lease->other_mineral_name;
            } elseif ($lease->minerals && $lease->minerals->isNotEmpty()) {
                $mineralName = $lease->minerals->pluck('name')->filter()->implode(', ');
            }

            // Create MasterProject
            $master = MasterProject::create([
                'master_application_id' => $masterId,
                'customer_id'           => $lease->customer_id,
                'branch_id'             => $lease->branch_id,
                'district_id'           => $lease->district_id,
                'taluk'                 => $lease->taluk,
                'village'               => $lease->village,
                'survey_numbers'        => $surveyNumbers,
                'area_extent_ha'        => $lease->area_extent_ha,
                'mineral_name'          => $mineralName,
                'lease_application_id'  => $lease->id,
                'current_stage'         => 'lease',
                'pipeline_status'       => 'in_progress',
                'created_by'            => $officer?->id ?? $lease->created_by,
            ]);

            // Update Lease with Master link
            $lease->update([
                'master_project_id' => $master->id,
                'common_id'         => $master->master_application_id,
            ]);

            // Record initial movement in audit log
            PipelineMovement::create([
                'master_project_id' => $master->id,
                'from_department'   => 'system',
                'to_department'     => 'lease',
                'action_type'       => 'Initiation',
                'priority'          => 'normal',
                'reason'            => 'Master project initialized from Lease Application #' . ($lease->application_no ?? $lease->id),
                'officer_id'        => $officer?->id,
                'officer_name'      => $officer?->name ?: 'System',
                'metadata'          => [
                    'lease_application_id' => $lease->id,
                    'application_no'       => $lease->application_no,
                    'customer_id'          => $lease->customer_id,
                ],
            ]);

            return $master->fresh();
        });
    }

    /**
     * Alias for createOrGetMasterProject to satisfy interface contracts.
     */
    public function createFromLease(LeaseApplication $lease, ?User $officer = null): MasterProject
    {
        return $this->createOrGetMasterProject($lease, $officer);
    }

    /**
     * Forward Master Project to the next department in the statutory pipeline.
     * Auto-fills departmental metadata and logs immutable audit trail.
     */
    public function forwardStage(
        MasterProject $master,
        string $toDepartment,
        array $payload,
        User $officer
    ): PipelineMovement {
        $normalizedTo = $this->normalizeDepartment($toDepartment);
        $currentStage = $this->normalizeDepartment($master->current_stage);

        // Validate forward direction
        if (!$this->isValidForwardTransition($currentStage, $normalizedTo)) {
            throw new InvalidArgumentException(
                "Cannot forward stage backward from '{$currentStage}' to '{$normalizedTo}'. Use returnForCorrection() instead."
            );
        }

        return DB::transaction(function () use ($master, $normalizedTo, $currentStage, $payload, $officer) {
            // Auto-fill target department parameters from master
            $autoFill = $this->getAutoFillData($master, $normalizedTo);

            // Associate department record ID if passed or newly created
            $this->associateDepartmentRecord($master, $normalizedTo, $payload, $autoFill);

            // Update Master stage & status
            $master->current_stage = $normalizedTo;
            $master->pipeline_status = 'in_progress';
            $master->save();

            // Record immutable movement
            return PipelineMovement::create([
                'master_project_id' => $master->id,
                'from_department'   => $currentStage,
                'to_department'     => $normalizedTo,
                'action_type'       => 'Forward_Transfer',
                'priority'          => $payload['priority'] ?? 'normal',
                'reason'            => $payload['reason'] ?? "Forwarded from {$currentStage} to {$normalizedTo}",
                'officer_id'        => $officer->id,
                'officer_name'      => $officer->name,
                'metadata'          => array_merge($payload['metadata'] ?? [], [
                    'auto_filled' => $autoFill,
                ]),
            ]);
        });
    }

    /**
     * Return active project stage back to an upstream department for correction.
     * Requires mandatory reason and priority flag; updates department records and dispatches alerts.
     */
    public function returnForCorrection(
        MasterProject $master,
        string $fromDepartment,
        string $toDepartment,
        string $reason,
        string $priority,
        ?string $mistakeCategory,
        User $officer,
        ?string $rectificationNotes = null
    ): PipelineMovement {
        $cleanReason = trim($reason);
        if ($cleanReason === '') {
            throw new InvalidArgumentException('A detailed reason for return is mandatory.');
        }

        $cleanPriority = strtolower(trim($priority));
        if (!in_array($cleanPriority, ['urgent', 'high', 'normal'], true)) {
            throw new InvalidArgumentException('Priority must be one of: urgent, high, normal.');
        }

        $normalizedFrom = $this->normalizeDepartment($fromDepartment);
        $normalizedTo = $this->normalizeDepartment($toDepartment);

        return DB::transaction(function () use (
            $master,
            $normalizedFrom,
            $normalizedTo,
            $cleanReason,
            $cleanPriority,
            $mistakeCategory,
            $officer,
            $rectificationNotes
        ) {
            // Update Master Project stage and status
            $master->current_stage = $normalizedTo;
            $master->pipeline_status = 'returned_for_correction';
            $master->save();

            // Update active department records status
            $this->updateDepartmentRecordStatus($master, $normalizedTo, 'returned_for_correction', $cleanReason);
            $this->updateDepartmentRecordStatus($master, $normalizedFrom, 'returned_for_correction', $cleanReason);

            // Record immutable audit movement
            $movement = PipelineMovement::create([
                'master_project_id'   => $master->id,
                'from_department'     => $normalizedFrom,
                'to_department'       => $normalizedTo,
                'action_type'         => 'Returned_For_Correction',
                'priority'            => $cleanPriority,
                'mistake_category'    => $mistakeCategory,
                'reason'              => $cleanReason,
                'rectification_notes' => $rectificationNotes,
                'officer_id'          => $officer->id,
                'officer_name'        => $officer->name,
                'metadata'            => [
                    'returned_at' => now()->toIso8601String(),
                ],
            ]);

            // Dispatch in-app notification alerts
            $this->dispatchReturnNotification($master, $normalizedFrom, $normalizedTo, $cleanReason, $cleanPriority, $mistakeCategory, $officer);

            return $movement;
        });
    }

    /**
     * Resubmit a corrected file back to the scrutiny department.
     */
    public function resubmitFix(
        MasterProject $master,
        string $fromDepartment,
        string $toDepartment,
        string $rectificationNotes,
        User $officer
    ): PipelineMovement {
        $normalizedFrom = $this->normalizeDepartment($fromDepartment);
        $normalizedTo = $this->normalizeDepartment($toDepartment);

        return DB::transaction(function () use ($master, $normalizedFrom, $normalizedTo, $rectificationNotes, $officer) {
            // Update Master Project stage and status
            $master->current_stage = $normalizedTo;
            $master->pipeline_status = 'in_progress';
            $master->save();

            // Update department record status back to in_progress
            $this->updateDepartmentRecordStatus($master, $normalizedFrom, 'in_progress', 'Corrections completed and resubmitted.');
            $this->updateDepartmentRecordStatus($master, $normalizedTo, 'in_progress', 'Under scrutiny post rectification.');

            // Record immutable movement
            return PipelineMovement::create([
                'master_project_id'   => $master->id,
                'from_department'     => $normalizedFrom,
                'to_department'       => $normalizedTo,
                'action_type'         => 'Resubmitted_Fix',
                'priority'            => 'normal',
                'reason'              => 'Corrections completed and resubmitted for scrutiny',
                'rectification_notes' => $rectificationNotes,
                'officer_id'          => $officer->id,
                'officer_name'        => $officer->name,
                'metadata'            => [
                    'resubmitted_at' => now()->toIso8601String(),
                ],
            ]);
        });
    }

    /**
     * Compute chronological timeline journey data, department durations, and bottlenecks.
     */
    public function getTimelineData(MasterProject $master): array
    {
        $movements = $master->movements()
            ->with('officer')
            ->orderBy('created_at', 'asc')
            ->get();

        $deptDurations = [];
        $deptReturns = [];
        $deptVisits = [];

        $previousTime = $master->created_at ?: now();
        $currentActiveDept = 'lease';

        foreach ($movements as $m) {
            $mTime = $m->created_at ?: now();
            $durationSeconds = max(0, $mTime->diffInSeconds($previousTime));

            // Attribute time to the department where the project resided prior to this movement
            $spentDept = ($m->from_department !== 'system' && !empty($m->from_department))
                ? $m->from_department
                : 'lease';

            $deptDurations[$spentDept] = ($deptDurations[$spentDept] ?? 0) + $durationSeconds;

            if ($m->isReturn()) {
                $targetDept = $m->to_department;
                $deptReturns[$targetDept] = ($deptReturns[$targetDept] ?? 0) + 1;
            }

            $deptVisits[$m->to_department] = ($deptVisits[$m->to_department] ?? 0) + 1;
            $previousTime = $mTime;
            $currentActiveDept = $m->to_department;
        }

        // Add duration from last movement to now() if project is still active
        if ($master->isActive()) {
            $durationToNow = max(0, now()->diffInSeconds($previousTime));
            $deptDurations[$currentActiveDept] = ($deptDurations[$currentActiveDept] ?? 0) + $durationToNow;
        }

        $totalSeconds = array_sum($deptDurations);
        $totalHours = round($totalSeconds / 3600, 2);
        $totalDays = round($totalSeconds / 86400, 2);

        // Build structured department metrics
        $departmentMetrics = [];
        foreach ($deptDurations as $dept => $sec) {
            $hrs = round($sec / 3600, 2);
            $departmentMetrics[$dept] = [
                'department'    => $dept,
                'label'         => $this->getDepartmentLabel($dept),
                'seconds'       => $sec,
                'hours'         => $hrs,
                'days'          => round($sec / 86400, 2),
                'percentage'    => $totalSeconds > 0 ? round(($sec / $totalSeconds) * 100, 1) : 0,
                'visit_count'   => $deptVisits[$dept] ?? 0,
                'returns_count' => $deptReturns[$dept] ?? 0,
                'has_returns'   => ($deptReturns[$dept] ?? 0) > 0,
            ];
        }

        // Bottlenecks calculation
        $slowestDepartment = null;
        $slowestHours = 0;
        foreach ($departmentMetrics as $dept => $metric) {
            if ($metric['hours'] >= $slowestHours) {
                $slowestHours = $metric['hours'];
                $slowestDepartment = $dept;
            }
        }

        $returnedDepartments = array_values(array_filter($departmentMetrics, fn($m) => $m['returns_count'] > 0));
        $totalReturns = array_sum(array_column($departmentMetrics, 'returns_count'));

        $avgHours = count($departmentMetrics) > 0 ? ($totalHours / count($departmentMetrics)) : 0;
        $bottleneckList = [];
        foreach ($departmentMetrics as $dept => $metric) {
            $reasons = [];
            if ($metric['returns_count'] > 0) {
                $reasons[] = "Returned for correction {$metric['returns_count']} time(s)";
            }
            if ($metric['hours'] > $avgHours && $totalHours > 0) {
                $reasons[] = "Exceeds average stage processing time ({$metric['hours']} hrs vs avg {$avgHours} hrs)";
            }
            if (!empty($reasons)) {
                $bottleneckList[$dept] = [
                    'department' => $dept,
                    'label'      => $metric['label'],
                    'reasons'    => $reasons,
                    'severity'   => $metric['returns_count'] > 1 ? 'critical' : ($metric['returns_count'] > 0 ? 'high' : 'medium'),
                ];
            }
        }

        return [
            'master_project'         => $master,
            'current_stage'          => $master->current_stage,
            'pipeline_status'        => $master->pipeline_status,
            'is_returned'            => $master->isReturnedForCorrection(),
            'total_duration_seconds' => $totalSeconds,
            'total_duration_hours'   => $totalHours,
            'total_duration_days'    => $totalDays,
            'total_duration_human'   => $this->formatHumanDuration($totalSeconds),
            'department_metrics'     => $departmentMetrics,
            'bottlenecks'            => [
                'slowest_department'   => $slowestDepartment,
                'slowest_hours'        => $slowestHours,
                'returned_departments' => $returnedDepartments,
                'total_returns'        => $totalReturns,
                'bottleneck_list'      => $bottleneckList,
            ],
            'movements'              => $movements,
        ];
    }

    /**
     * Auto-fill helper extracting master project data for target department models.
     */
    public function getAutoFillData(MasterProject $master, ?string $targetDepartment = null): array
    {
        $base = [
            'customer_id'       => $master->customer_id,
            'branch_id'         => $master->branch_id,
            'district_id'       => $master->district_id,
            'taluk'             => $master->taluk,
            'village'           => $master->village,
            'survey_numbers'    => $master->survey_numbers,
            'area_extent_ha'    => $master->area_extent_ha,
            'mineral_name'      => $master->mineral_name,
            'common_id'         => $master->master_application_id,
            'master_project_id' => $master->id,
            'customer_name'     => $master->customer?->customer_name,
            'company_name'      => $master->customer?->company_name,
            'mobile_num'        => $master->customer?->mobile_num,
            'district_name'     => $master->district?->name,
        ];

        if (!$targetDepartment) {
            return $base;
        }

        $dept = $this->normalizeDepartment($targetDepartment);

        return match ($dept) {
            'survey_dgps', 'dgps' => array_merge($base, [
                'location'      => trim("{$master->village}, {$master->taluk}"),
                'lease_area_ha' => (float) $master->area_extent_ha,
                'survey_no'     => $master->survey_numbers,
            ]),
            'survey_drone', 'drone' => array_merge($base, [
                'location'   => trim("{$master->village}, {$master->taluk}"),
                'lease_area' => (float) $master->area_extent_ha,
                'survey_no'  => $master->survey_numbers,
            ]),
            'mining' => array_merge($base, [
                'survey_numbers_text'  => $master->survey_numbers,
                'lease_application_id' => $master->lease_application_id,
            ]),
            'environment' => array_merge($base, [
                'location'              => trim("{$master->village}, {$master->taluk}"),
                'project_name'          => 'Quarry Project - ' . trim("{$master->village}, {$master->taluk}"),
                'lease_application_id'  => $master->lease_application_id,
                'mining_application_id' => $master->mining_application_id,
            ]),
            'ppt' => array_merge($base, [
                'taluk_village'          => trim("{$master->taluk} / {$master->village}"),
                'project_name'           => 'Quarry Project - ' . trim("{$master->village}, {$master->taluk}"),
                'environment_project_id' => $master->environment_project_id,
            ]),
            default => $base,
        };
    }

    /**
     * Associate new or passed department record ID on MasterProject and synchronize foreign keys.
     */
    protected function associateDepartmentRecord(
        MasterProject $master,
        string $department,
        array $payload,
        array $autoFill
    ): void {
        $keyMap = [
            'survey_dgps'  => 'dgps_survey_id',
            'survey_drone' => 'drone_survey_id',
            'mining'       => 'mining_application_id',
            'environment'  => 'environment_project_id',
            'ppt'          => 'ppt_application_id',
        ];

        $targetKey = $keyMap[$department] ?? null;

        // Check if an explicit ID was passed
        $recordId = $payload[$targetKey] ?? ($payload['record_id'] ?? null);

        // Check if an Eloquent model instance was passed
        if (isset($payload['record']) && $payload['record'] instanceof Model) {
            $record = $payload['record'];
            $recordId = $record->id;

            // Ensure mutual link
            $recordUpdates = [];
            if (Schema::hasColumn($record->getTable(), 'master_project_id')) {
                $recordUpdates['master_project_id'] = $master->id;
            }
            if (Schema::hasColumn($record->getTable(), 'common_id')) {
                $recordUpdates['common_id'] = $master->master_application_id;
            }
            if (!empty($recordUpdates)) {
                $record->update($recordUpdates);
            }
        }

        if ($targetKey && $recordId) {
            $master->{$targetKey} = $recordId;
        }
    }

    /**
     * Dispatch real-time in-app priority alert notifications.
     */
    protected function dispatchReturnNotification(
        MasterProject $master,
        string $fromDepartment,
        string $toDepartment,
        string $reason,
        string $priority,
        ?string $mistakeCategory,
        User $officer
    ): void {
        $recipients = collect();

        // 1. Creator of target department application
        $targetModel = $this->getDepartmentModel($master, $toDepartment);
        if ($targetModel && !empty($targetModel->created_by)) {
            $u = User::find($targetModel->created_by);
            if ($u) {
                $recipients->push($u);
            }
        }

        // 2. Creator of master project
        if (!empty($master->created_by)) {
            $u = User::find($master->created_by);
            if ($u) {
                $recipients->push($u);
            }
        }

        // 3. Branch officers if available
        if ($recipients->isEmpty() && $master->branch_id) {
            $branchUsers = User::where('branch_id', $master->branch_id)->get();
            $recipients = $recipients->merge($branchUsers);
        }

        // 4. Fallback to acting officer
        if ($recipients->isEmpty()) {
            $recipients->push($officer);
        }

        /** @var Collection<int, User> $uniqueRecipients */
        $uniqueRecipients = $recipients->unique('id');

        $actionUrl = Route::has('master-pipeline.timeline')
            ? route('master-pipeline.timeline', $master->id)
            : url("/master-pipeline/{$master->id}/timeline");

        foreach ($uniqueRecipients as $recipient) {
            Notification::create([
                'id'              => (string) Str::uuid(),
                'type'            => 'App\Notifications\ReturnedForCorrection',
                'notifiable_type' => User::class,
                'notifiable_id'   => $recipient->id,
                'data'            => [
                    'title'                 => "File Returned for Correction: {$master->master_application_id}",
                    'message'               => $reason,
                    'category'              => $toDepartment,
                    'priority'              => $priority,
                    'mistake_category'      => $mistakeCategory,
                    'from_department'       => $fromDepartment,
                    'to_department'         => $toDepartment,
                    'master_project_id'     => $master->id,
                    'master_application_id' => $master->master_application_id,
                    'action_url'            => $actionUrl,
                    'icon'                  => 'bi-exclamation-triangle-fill',
                ],
                'read_at'         => null,
            ]);
        }
    }

    /**
     * Update department record status and notes safely.
     */
    protected function updateDepartmentRecordStatus(
        MasterProject $master,
        string $department,
        string $status,
        ?string $notes = null
    ): void {
        $model = $this->getDepartmentModel($master, $department);
        if (!$model) {
            return;
        }

        $updates = [];
        $table = $model->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $updates['status'] = $status;
        } elseif (Schema::hasColumn($table, 'survey_status')) {
            $updates['survey_status'] = $status;
        }

        if ($notes !== null) {
            if (Schema::hasColumn($table, 'status_notes')) {
                $updates['status_notes'] = $notes;
            } elseif (Schema::hasColumn($table, 'rejection_note')) {
                $updates['rejection_note'] = $notes;
            }
        }

        if (!empty($updates)) {
            $model->update($updates);
        }
    }

    /**
     * Resolve the active Eloquent model instance for a department stage.
     */
    public function getDepartmentModel(MasterProject $master, string $department): ?Model
    {
        $dept = $this->normalizeDepartment($department);
        return match ($dept) {
            'lease'        => $master->leaseApplication,
            'survey_dgps'  => $master->dgpsSurvey,
            'survey_drone' => $master->droneSurvey,
            'mining'       => $master->miningApplication,
            'environment'  => $master->environmentProject,
            'ppt'          => $master->pptApplication,
            default        => null,
        };
    }

    /**
     * Check if a stage transition is forward.
     */
    public function isValidForwardTransition(string $fromDepartment, string $toDepartment): bool
    {
        $fromRank = $this->stageOrder[$this->normalizeDepartment($fromDepartment)] ?? 0;
        $toRank = $this->stageOrder[$this->normalizeDepartment($toDepartment)] ?? 0;

        if ($fromRank === 0 || $toRank === 0) {
            return true; // Allow custom stages
        }

        return $toRank >= $fromRank;
    }

    /**
     * Normalize department stage identifier.
     */
    public function normalizeDepartment(string $department): string
    {
        return match (strtolower(trim($department))) {
            'lease', 'lease_application', 'la'                  => 'lease',
            'dgps', 'dgps_survey', 'survey_dgps'                => 'survey_dgps',
            'drone', 'drone_survey', 'survey_drone'             => 'survey_drone',
            'survey'                                            => 'survey',
            'mining', 'mining_plan', 'mining_application', 'mp' => 'mining',
            'environment', 'environment_project', 'ec', 'env'   => 'environment',
            'ppt', 'ppt_application'                            => 'ppt',
            'completed', 'finish', 'done'                       => 'completed',
            default                                             => strtolower(trim($department)),
        };
    }

    /**
     * Human-readable department stage label.
     */
    public function getDepartmentLabel(string $department): string
    {
        return match ($this->normalizeDepartment($department)) {
            'lease'        => 'Lease Application',
            'survey_dgps'  => 'DGPS Land Survey',
            'survey_drone' => 'Drone Survey',
            'survey'       => 'Land & Drone Survey',
            'mining'       => 'Mining Plan Preparation',
            'environment'  => 'Environmental Clearance',
            'ppt'          => 'SEAC PPT Presentation',
            'completed'    => 'Clearance Completed',
            default        => ucwords(str_replace('_', ' ', $department)),
        };
    }

    /**
     * Format seconds into human readable duration string.
     */
    protected function formatHumanDuration(int|float $seconds): string
    {
        $seconds = (int) $seconds;
        if ($seconds < 60) {
            return "{$seconds}s";
        }
        $days = (int) floor($seconds / 86400);
        $hours = (int) floor(($seconds % 86400) / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days}d";
        }
        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0 && $days === 0) {
            $parts[] = "{$minutes}m";
        }

        return !empty($parts) ? implode(' ', $parts) : '0s';
    }
}
