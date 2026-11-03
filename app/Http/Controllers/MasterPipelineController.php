<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LeaseApplication;
use App\Models\MasterProject;
use App\Services\MasterPipelineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MasterPipelineController extends Controller
{
    public function __construct(
        protected MasterPipelineService $pipelineService
    ) {}

    /**
     * Display a paginated catalog of Master Projects with KPI metrics & branch filtering.
     */
    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id');
        $stage    = $request->input('stage');
        $status   = $request->input('status');
        $search   = trim((string) $request->input('search', ''));

        // Base KPI Query respecting active branch tenancy
        $kpiQuery = MasterProject::query();
        if (!empty($branchId)) {
            $kpiQuery->where('branch_id', $branchId);
        }

        $kpis = [
            'total_projects'          => (clone $kpiQuery)->count(),
            'active_pipeline'         => (clone $kpiQuery)->active()->count(),
            'in_progress'             => (clone $kpiQuery)->where('pipeline_status', 'in_progress')->count(),
            'returned_for_correction' => (clone $kpiQuery)->where('pipeline_status', 'returned_for_correction')->count(),
            'completed'               => (clone $kpiQuery)->where('pipeline_status', 'completed')->count(),
        ];

        // Filterable projects query with eager loaded relationships
        $query = MasterProject::with([
            'customer',
            'branch',
            'district',
            'leaseApplication',
            'dgpsSurvey',
            'droneSurvey',
            'miningApplication',
            'environmentProject',
            'pptApplication',
            'latestMovement',
        ]);

        if ($search !== '') {
            $query->search($search);
        }

        if (!empty($stage)) {
            $query->stage($stage);
        }

        if (!empty($status)) {
            $query->where('pipeline_status', $status);
        }

        if (!empty($branchId)) {
            $query->where('branch_id', $branchId);
        }

        $projects = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('branch_name')->get();

        $stages = [
            'lease'        => 'Lease Application',
            'survey_dgps'  => 'DGPS Land Survey',
            'survey_drone' => 'Drone Survey',
            'mining'       => 'Mining Plan Preparation',
            'environment'  => 'Environmental Clearance',
            'ppt'          => 'SEAC PPT Presentation',
            'completed'    => 'Completed',
        ];

        $statuses = [
            'in_progress'             => 'In Progress',
            'returned_for_correction' => 'Returned for Correction',
            'under_scrutiny'          => 'Under Scrutiny',
            'completed'               => 'Completed',
        ];

        return view('pages.master_pipeline.index', compact(
            'projects',
            'kpis',
            'branches',
            'stages',
            'statuses',
            'branchId',
            'stage',
            'status',
            'search'
        ));
    }

    /**
     * Display the 360° Visual Master Project Timeline Dashboard.
     */
    public function timeline(int|string $id): View
    {
        $master = MasterProject::with([
            'customer',
            'branch',
            'district',
            'leaseApplication',
            'dgpsSurvey',
            'droneSurvey',
            'miningApplication',
            'environmentProject',
            'pptApplication',
            'movements.officer',
        ])->findOrFail($id);

        $timelineData = $this->pipelineService->getTimelineData($master);

        $departments = [
            'lease' => [
                'name'  => 'Lease Application',
                'code'  => 'LA',
                'model' => $master->leaseApplication,
                'no'    => $master->leaseApplication?->application_no ?? 'Pending',
                'status'=> $master->leaseApplication?->status ?? 'Not Initiated',
                'stage' => 'lease',
            ],
            'survey_dgps' => [
                'name'  => 'DGPS Survey',
                'code'  => 'DGPS',
                'model' => $master->dgpsSurvey,
                'no'    => $master->dgpsSurvey?->application_number ?? ($master->dgpsSurvey ? 'DGPS-' . $master->dgpsSurvey->id : 'Pending'),
                'status'=> $master->dgpsSurvey?->survey_status ?? 'Not Initiated',
                'stage' => 'survey_dgps',
            ],
            'survey_drone' => [
                'name'  => 'Drone Photogrammetry',
                'code'  => 'DRN',
                'model' => $master->droneSurvey,
                'no'    => $master->droneSurvey?->application_number ?? ($master->droneSurvey ? 'DRN-' . $master->droneSurvey->id : 'Pending'),
                'status'=> $master->droneSurvey?->survey_status ?? 'Not Initiated',
                'stage' => 'survey_drone',
            ],
            'mining' => [
                'name'  => 'Mining Plan',
                'code'  => 'MP',
                'model' => $master->miningApplication,
                'no'    => $master->miningApplication?->application_number ?? ($master->miningApplication ? 'MP-' . $master->miningApplication->id : 'Pending'),
                'status'=> $master->miningApplication?->status ?? 'Not Initiated',
                'stage' => 'mining',
            ],
            'environment' => [
                'name'  => 'Environmental Clearance',
                'code'  => 'ENV',
                'model' => $master->environmentProject,
                'no'    => $master->environmentProject?->project_code ?? ($master->environmentProject ? 'ENV-' . $master->environmentProject->id : 'Pending'),
                'status'=> $master->environmentProject?->status ?? 'Not Initiated',
                'stage' => 'environment',
            ],
            'ppt' => [
                'name'  => 'PPT Presentation',
                'code'  => 'PPT',
                'model' => $master->pptApplication,
                'no'    => $master->pptApplication?->application_no ?? ($master->pptApplication ? 'PPT-' . $master->pptApplication->id : 'Pending'),
                'status'=> $master->pptApplication?->status ?? 'Not Initiated',
                'stage' => 'ppt',
            ],
        ];

        return view('pages.master_pipeline.timeline', compact(
            'master',
            'timelineData',
            'departments'
        ));
    }

    /**
     * Promote an existing Lease Application to a Master Project.
     */
    public function createFromLease(Request $request, int|string $leaseId): RedirectResponse|JsonResponse
    {
        $lease = LeaseApplication::findOrFail($leaseId);
        $master = $this->pipelineService->createOrGetMasterProject($lease, Auth::user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Master Project {$master->master_application_id} initialized successfully.",
                'data'    => $master,
                'url'     => route('master-pipeline.timeline', $master->id),
            ]);
        }

        return redirect()->route('master-pipeline.timeline', $master->id)
            ->with('success', "Master Project {$master->master_application_id} initialized successfully.");
    }

    /**
     * Forward Master Project stage forward.
     */
    public function forward(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $master = MasterProject::findOrFail($id);

        $validated = $request->validate([
            'to_department' => 'required|string',
            'priority'      => 'nullable|string|in:urgent,high,normal,low',
            'reason'        => 'nullable|string',
        ]);

        try {
            $movement = $this->pipelineService->forwardStage(
                $master,
                $validated['to_department'],
                $request->all(),
                Auth::user()
            );

            $label = $this->pipelineService->getDepartmentLabel($validated['to_department']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Stage successfully advanced to {$label}.",
                    'movement' => $movement,
                ]);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('success', "Stage successfully advanced to {$label}.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Return downstream project back to an upstream department for correction.
     */
    public function returnForCorrection(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $master = MasterProject::findOrFail($id);

        $validated = $request->validate([
            'from_department'     => 'required|string',
            'to_department'       => 'required|string',
            'reason'              => 'required|string|min:5',
            'priority'            => 'required|in:normal,high,urgent',
            'mistake_category'    => 'nullable|string',
            'rectification_notes' => 'nullable|string',
        ]);

        try {
            $movement = $this->pipelineService->returnForCorrection(
                $master,
                $validated['from_department'],
                $validated['to_department'],
                $validated['reason'],
                $validated['priority'],
                $validated['mistake_category'] ?? null,
                Auth::user(),
                $validated['rectification_notes'] ?? null
            );

            $targetLabel = $this->pipelineService->getDepartmentLabel($validated['to_department']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Project returned for correction to {$targetLabel}. Real-time alerts dispatched.",
                    'movement' => $movement,
                ]);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('alert', "Project returned for correction to {$targetLabel}. Real-time alerts dispatched.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Resubmit corrected file back to scrutiny department.
     */
    public function resubmit(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $master = MasterProject::findOrFail($id);

        $validated = $request->validate([
            'from_department'     => 'required|string',
            'to_department'       => 'required|string',
            'rectification_notes' => 'required|string',
        ]);

        try {
            $movement = $this->pipelineService->resubmitFix(
                $master,
                $validated['from_department'],
                $validated['to_department'],
                $validated['rectification_notes'],
                Auth::user()
            );

            $targetLabel = $this->pipelineService->getDepartmentLabel($validated['to_department']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => "Corrections resubmitted successfully to {$targetLabel}.",
                    'movement' => $movement,
                ]);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('success', "Corrections resubmitted successfully to {$targetLabel}.");
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('master-pipeline.timeline', $master->id)
                ->with('error', $e->getMessage());
        }
    }
}
