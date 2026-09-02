<?php

namespace App\Http\Controllers;

use App\Models\EnvironmentalActivity;
use App\Models\EnvironmentalDocument;
use App\Models\EnvironmentalProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnvironmentalB2Controller extends Controller
{
    private const CHECKLIST = [
        'Documents' => ['500m Radius Letter', 'Existing Pit Letter', 'Approved Mining Plan Book', 'Approved Letter', '300m Radius VAO Statement', 'School CER', 'NOC Letters', 'Others (PAN, contact, Aadhaar verification, EC list)'],
        'Site Photographs' => ['DGPS Photograph', 'Fencing Photograph', 'Greenbelt Photograph'],
        'Report' => ['Location Details', 'Covering Letter', 'Front Page', 'Form-1', 'Pre-feasibility Report', 'Baseline Study Report', 'Hydrogeological Report', 'Affidavit', 'Checklist', 'B2 Category Checklist', 'PPT'],
        'GIS' => ['GIS Data'],
        'Upload Signed Reports' => ['Signed Reports'],
        'PARIVESH Online Registration' => ['Note Pad (User ID / Password)', 'Common Application Form', 'Form 2', 'Payment Receipt', 'SPCB Demand Note'],
    ];

    public function index()
    {
        return view('pages.enviro_b2.index');
    }

    public function wizard(int $step)
    {
        abort_unless($step >= 1 && $step <= 7, 404);
        return view('pages.enviro_b2.wizard', compact('step'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255', 'project_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255', 'district' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255', 'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ]);
        $project = DB::transaction(function () use ($data) {
            $project = EnvironmentalProject::create($data + ['project_code' => 'ECB2-'.now()->format('Y').'-'.str_pad((string) (EnvironmentalProject::max('id') + 1), 4, '0', STR_PAD_LEFT)]);
            foreach (self::CHECKLIST as $folder => $documents) {
                foreach ($documents as $document) EnvironmentalDocument::create(['project_id' => $project->id, 'folder' => $folder, 'document_name' => $document]);
            }
            $this->log($project, 'Project created', 'B2 document checklist generated.');
            return $project;
        });
        return redirect()->route('environment-b2.show', $project)->with('success', 'B2 project and checklist created.');
    }

    public function show(EnvironmentalProject $project)
    {
        $project->load(['documents' => fn ($q) => $q->orderBy('folder')->orderBy('id'), 'activities']);
        $folders = $project->documents->groupBy('folder');
        $summary = ['total' => $project->documents->count(), 'uploaded' => $project->documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count(), 'approved' => $project->documents->where('status', 'approved')->count()];
        return view('pages.enviro_b2.show', compact('project', 'folders', 'summary'));
    }

    public function upload(Request $request, EnvironmentalDocument $document)
    {
        $data = $request->validate(['file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,zip|max:10240']);
        if ($document->file_path) Storage::disk('public')->delete($document->file_path);
        $file = $data['file'];
        $path = $file->store('environment-b2/'.$document->project_id, 'public');
        $document->update(['file_path' => $path, 'original_name' => $file->getClientOriginalName(), 'status' => 'uploaded', 'review_note' => null, 'uploaded_at' => now(), 'validated_at' => null, 'approved_at' => null]);
        $this->log($document->project, 'Document uploaded', $document->document_name);
        return back()->with('success', 'Document uploaded and sent for validation.');
    }

    public function review(Request $request, EnvironmentalDocument $document)
    {
        $data = $request->validate(['status' => 'required|in:validated,approved,revision_required', 'review_note' => 'nullable|string|max:2000']);
        if (!$document->file_path) return back()->with('error', 'Upload a document before reviewing it.');
        $document->update($data + [
            'validated_at' => in_array($data['status'], ['validated', 'approved']) ? now() : null,
            'approved_at' => $data['status'] === 'approved' ? now() : null,
        ]);
        $this->log($document->project, 'Document '.$data['status'], $document->document_name.($data['review_note'] ? ': '.$data['review_note'] : ''));
        return back()->with('success', 'Document review saved.');
    }

    public function updateStatus(Request $request, EnvironmentalProject $project)
    {
        $data = $request->validate(['status' => 'required|in:draft,validation,approved,reported,archived']);
        $status = $data['status'];
        if (in_array($status, ['validation', 'approved']) && !$project->documents()->whereNotIn('status', ['validated', 'approved'])->doesntExist()) return back()->with('error', 'All checklist documents must be validated before this stage.');
        if ($status === 'approved' && !$project->documents()->where('status', 'approved')->exists()) return back()->with('error', 'Approve at least one validated document before approval.');
        $project->update(['status' => $status, 'validated_at' => $status === 'validation' ? now() : $project->validated_at, 'approved_at' => $status === 'approved' ? now() : $project->approved_at, 'archived_at' => $status === 'archived' ? now() : null]);
        $this->log($project, 'Project moved to '.str_replace('_', ' ', $status));
        return back()->with('success', 'Project workflow updated.');
    }

    public function download(EnvironmentalDocument $document)
    {
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);
        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    private function log(EnvironmentalProject $project, string $action, ?string $details = null): void
    {
        EnvironmentalActivity::create(['project_id' => $project->id, 'action' => $action, 'details' => $details]);
    }
}
