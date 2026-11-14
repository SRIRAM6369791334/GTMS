# 🧩 GTMS New Page Boilerplate & Recipes
> **Token-Saver Reference:** When creating any new feature or page in GTMS, copy-paste from these certified templates instead of exploring existing controllers and views. Guarantees 100% compliance with GTMS UI/UX Pro Max, Spatie RBAC, and Branch Multi-Tenancy.

---

## 1. Database Migration & Eloquent Model

### Migration Template (`create_equipments_table.php`)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Business Fields
            $table->string('equipment_code', 50)->unique();
            $table->string('name', 255);
            $table->string('model_no', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->tinyInteger('status')->default(1); // 1 = Active, 0 = Inactive
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('branch_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
```

### Model Template (`app/Models/Equipment.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;

    protected $table = 'equipments';
    protected $guarded = [];

    // Auto-scope to current user's branch for non-admins
    protected static function booted(): void
    {
        static::addGlobalScope('branch_scope', function ($builder) {
            $user = auth()->user();
            if ($user && !$user->hasRole(['Admin', 'Super Admin']) && $user->branch_id) {
                $builder->where('branch_id', $user->branch_id);
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                if (empty($model->created_by)) {
                    $model->created_by = auth()->id();
                }
                if (empty($model->branch_id) && auth()->user()->branch_id) {
                    $model->branch_id = auth()->user()->branch_id;
                }
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
```

---

## 2. Controller Blueprint (`app/Http/Controllers/EquipmentController.php`)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Branch;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['branch', 'creator'])->latest()->get();
        $totalCount = $equipments->count();
        $activeCount = $equipments->where('status', 1)->count();
        $inactiveCount = $equipments->where('status', 0)->count();
        $branches = Branch::where('status', 1)->get();

        return view('pages.equipment.index', compact(
            'equipments', 'totalCount', 'activeCount', 'inactiveCount', 'branches'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_code' => 'required|string|max:50|unique:equipments,equipment_code',
            'name'           => 'required|string|max:255',
            'cost'           => 'nullable|numeric|min:0',
            'branch_id'      => 'nullable|exists:branches,id',
        ]);

        $item = Equipment::create([
            'equipment_code' => trim($request->equipment_code),
            'name'           => trim($request->name),
            'model_no'       => trim($request->model_no ?? ''),
            'purchase_date'  => $request->purchase_date,
            'cost'           => $request->cost ?? 0,
            'branch_id'      => $request->branch_id ?? auth()->user()?->branch_id,
            'status'         => $request->input('status', 1),
            'created_by'     => auth()->id(),
        ]);

        return response()->json([
            'status'  => 1,
            'message' => 'Equipment registered successfully',
            'data'    => $item
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|exists:equipments,id',
            'equipment_code' => 'required|string|max:50|unique:equipments,equipment_code,' . $request->id,
            'name'           => 'required|string|max:255',
            'status'         => 'nullable|in:0,1',
        ]);

        $item = Equipment::findOrFail($request->id);
        $item->update($request->only(['equipment_code', 'name', 'model_no', 'purchase_date', 'cost', 'status', 'branch_id']));

        return response()->json([
            'status'  => 1,
            'message' => 'Equipment updated successfully',
            'data'    => $item
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|exists:equipments,id']);

        $item = Equipment::findOrFail($request->id);
        $item->delete();

        return response()->json([
            'status'  => 1,
            'message' => 'Equipment deleted successfully'
        ]);
    }
}
```

---

## 3. Routes & RBAC Registration

### Route Entry (`routes/web.php`)
```php
// Equipment Management
Route::middleware('permission:equipment.view')->group(function () {
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
});
Route::post('equipmentadd', [EquipmentController::class, 'store'])
    ->name('equipmentadd')->middleware('permission:equipment.create');
Route::post('equipmentedit', [EquipmentController::class, 'update'])
    ->name('equipmentedit')->middleware('permission:equipment.edit');
Route::post('equipmentdelete', [EquipmentController::class, 'destroy'])
    ->name('equipmentdelete')->middleware('permission:equipment.delete');
```

### Module Registration (`app/Http/Controllers/RolesController.php`)
In the `$modules` array inside `RolesController@index`:
```php
$modules = [
    // existing modules...
    'equipment' => 'Equipment Management', // 👈 Adds dynamic permission category to /roles matrix
];
```

### Database Permissions Seeding (`php artisan tinker` or seeder):
```php
use Spatie\Permission\Models\Permission;

$perms = ['equipment.view', 'equipment.create', 'equipment.edit', 'equipment.delete'];
foreach ($perms as $p) {
    Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
}
```

---

## 4. UI/UX Pro Max Blade Template (`resources/views/pages/equipment/index.blade.php`)

```html
@extends('layouts.app')
@section('title', 'Equipment Directory')
@section('main_content')

<div class="content-body default-height">
    <div class="container-fluid">

        <!-- 1. Executive Top Bar -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">Equipment</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0 text-dark"><i class="fa fa-tools me-2 text-primary"></i> Equipment Registry</h1>
                <p class="text-muted small mb-0">Hardware, field survey instruments, and department assets.</p>
            </div>
            <div>
                @can('equipment.create')
                <button type="button" class="btn btn-primary px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#equipmentModal">
                    <i class="fa fa-plus me-1"></i> Add Equipment
                </button>
                @endcan
            </div>
        </div>

        <!-- 2. Bento KPI Metric Cards (4 Cards Grid) -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold small">Total Units</span>
                            <h2 class="fw-bold mb-0 mt-1" id="kpi_total">{{ $totalCount }}</h2>
                        </div>
                        <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-4"><i class="fa fa-boxes-stacked"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold small">Operational</span>
                            <h2 class="fw-bold mb-0 mt-1 text-success" id="kpi_active">{{ $activeCount }}</h2>
                        </div>
                        <div class="p-3 bg-success-subtle text-success rounded-3 fs-4"><i class="fa fa-check-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold small">Decommissioned</span>
                            <h2 class="fw-bold mb-0 mt-1 text-warning" id="kpi_inactive">{{ $inactiveCount }}</h2>
                        </div>
                        <div class="p-3 bg-warning-subtle text-warning rounded-3 fs-4"><i class="fa fa-pause-circle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold small">Branch Allocation</span>
                            <h2 class="fw-bold mb-0 mt-1 text-info">{{ $branches->count() }}</h2>
                        </div>
                        <div class="p-3 bg-info-subtle text-info rounded-3 fs-4"><i class="fa fa-building"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Main Data Panel -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <!-- SaaS Toolbar -->
            <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 360px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="custom_search" class="form-control border-start-0 ps-0" placeholder="Search equipment...">
                        <button class="btn btn-outline-secondary border-start-0" type="button" id="search_clear" style="display:none;"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <select id="filter_status" class="form-select" style="width: auto;">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <button type="button" id="btn_reset" class="btn btn-light"><i class="fa fa-undo me-1"></i> Reset</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive p-0">
                <table id="example10" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Code</th>
                            <th>Equipment Name</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th class="text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipments as $item)
                        <tr id="row_{{ $item->id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge bg-secondary-subtle text-dark font-monospace">{{ $item->equipment_code }}</span></td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>{{ $item->branch?->branch_name ?? 'Headquarters' }}</td>
                            <td>
                                @if($item->status == 1)
                                    <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @can('equipment.edit')
                                    <button class="btn btn-outline-primary btn-edit" 
                                        data-id="{{ $item->id }}"
                                        data-code="{{ $item->equipment_code }}"
                                        data-name="{{ $item->name }}"
                                        data-status="{{ $item->status }}">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    @endcan
                                    @can('equipment.delete')
                                    <button class="btn btn-outline-danger btn-delete" data-id="{{ $item->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Component -->
@include('pages.equipment.modal')
@endsection

@section('scripts')
<script src="{{ asset('js/ajax/equipment.js') }}"></script>
@endsection
```

---

## 5. Standard AJAX JavaScript (`public/js/ajax/equipment.js`)

```javascript
$(document).ready(function () {
    // CSRF Setup
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Destroy duplicate init
    if ($.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable().destroy();
    }

    var table = $('#example10').DataTable({
        dom: 't<"d-flex justify-content-between align-items-center p-3"ip>',
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']]
    });

    // Custom Live Search
    $('#custom_search').on('input', function () {
        var val = $(this).val();
        table.search(val).draw();
        $('#search_clear').toggle(val.length > 0);
    });

    $('#search_clear').on('click', function () {
        $('#custom_search').val('');
        table.search('').draw();
        $(this).hide();
    });

    // Form Submission (Add)
    $('#equipmentAddForm').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '/equipmentadd',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                if (res.status === 1) {
                    Swal.fire('Success', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON?.message || 'Validation failed';
                Swal.fire('Validation Error', msg, 'error');
            }
        });
    });

    // Delete Action
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Equipment?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('/equipmentdelete', { id: id }, function (res) {
                    if (res.status === 1) {
                        $('#row_' + id).fadeOut(300, function () { $(this).remove(); });
                        Swal.fire('Deleted!', res.message, 'success');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    });
});
```
