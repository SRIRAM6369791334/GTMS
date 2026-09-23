<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\MiningController;
use App\Http\Controllers\EnverionsoneController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EnvironmentalB2Controller;
use App\Http\Controllers\PptDepartmentController;
use App\Http\Controllers\EcCertificateController;
use App\Http\Controllers\DgpsSurveyController;
use App\Http\Controllers\DroneSurveyController;
use App\Http\Controllers\EcComplianceController;

use App\Http\Controllers\CustomerDirectoryController;
use App\Http\Controllers\CustomerTrackingController;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

});

// Authenticated System Routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('pages.index');
    })->name('dashboard');

    // Customer Directory (Live Dynamic CRUD)
    Route::middleware('permission:customer.view')->group(function () {
        Route::get('/customers', [CustomerDirectoryController::class, 'index'])->name('customers.index');
        Route::get('/customers/{slug}', [CustomerDirectoryController::class, 'show'])->name('customers.show');
        Route::get('/customer-tracking', [CustomerTrackingController::class, 'index'])->name('customer-tracking.index');
        Route::get('/customer-tracking/search', [CustomerTrackingController::class, 'search'])->name('customer-tracking.search');
        Route::get('/customer-tracking/{customer}', [CustomerTrackingController::class, 'show'])->name('customer-tracking.show');
        Route::get('/customer-tracking/{customer}/proforma-invoice', [CustomerTrackingController::class, 'proformaInvoice'])->name('customer-tracking.proforma-invoice');
        Route::get('/customer-tracking/{customer}/tax-invoice', [CustomerTrackingController::class, 'taxInvoice'])->name('customer-tracking.tax-invoice');
    });
    Route::get('/customers/lookup-mimas/{mimas_no}', [CustomerDirectoryController::class, 'lookupByMimas'])->name('customers.lookup.mimas');
    Route::post('/customeradd', [CustomerDirectoryController::class, 'store'])->name('customeradd')->middleware('permission:customer.create');
    Route::post('/customeredit', [CustomerDirectoryController::class, 'update'])->name('customeredit')->middleware('permission:customer.edit');
    Route::post('/customerdelete', [CustomerDirectoryController::class, 'destroy'])->name('customerdelete')->middleware('permission:customer.delete');


    // Role routes
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
        Route::get('roles/{id}/permissions', [RolesController::class, 'getPermissions'])->name('roles.permissions');
    });
    Route::post('roleadd', [RolesController::class, 'store'])->name('roleadd')->middleware('permission:roles.create');
    Route::post('roleupdate', [RolesController::class, 'update'])->name('roleupdate')->middleware('permission:roles.edit');
    Route::post('roledelete', [RolesController::class, 'destroy'])->name('roledelete')->middleware('permission:roles.delete');

    // User routes
    Route::middleware('permission:users.view')->group(function () {
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
    });
    Route::post('useradd', [UserController::class, 'store'])->name('useradd')->middleware('permission:users.create');
    Route::post('useredit', [UserController::class, 'update'])->name('useredit')->middleware('permission:users.edit');
    Route::post('userdelete', [UserController::class, 'destroy'])->name('userdelete')->middleware('permission:users.delete');

    // Branch / Department routes
    Route::middleware('permission:branch.view')->group(function () {
        Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
    });
    Route::post('branchadd', [BranchController::class, 'store'])->name('branchadd')->middleware('permission:branch.create');
    Route::post('branchedit', [BranchController::class, 'update'])->name('branchedit')->middleware('permission:branch.edit');
    Route::post('branchdelete', [BranchController::class, 'destroy'])->name('branchdelete')->middleware('permission:branch.delete');

    // Category routes
    Route::resource('/category', CategoryController::class)->only('index')->middleware('permission:category.view');
    Route::post('categoryadd', [CategoryController::class, 'store'])->name('categoryadd')->middleware('permission:category.create');
    Route::post('categoryedit', [CategoryController::class, 'update'])->name('categoryedit')->middleware('permission:category.edit');
    Route::post('categorydelete', [CategoryController::class, 'destroy'])->name('categorydelete')->middleware('permission:category.delete');

    // Unit routes
    Route::resource('/unit', UnitController::class)->only('index')->middleware('permission:unit.view');

    // Product routes
    Route::resource('/product', ProductController::class)->only('index')->middleware('permission:product.view');
    Route::post('productadd', [ProductController::class, 'store'])->name('productadd')->middleware('permission:product.create');

    // Product Stock routes
    Route::resource('/productstock', ProductStockController::class)->only('index')->middleware('permission:product.view');

    // Customer / Lease Application routes
    Route::middleware('permission:application.view')->group(function () {
        Route::get('/application', [CustomerController::class, 'index'])->name('application.index');
        Route::get('/viewapplication', [CustomerController::class, 'viewApplication'])->name('viewapplication');
    });
    Route::middleware('permission:application.create')->group(function () {
        Route::get('/step1', [CustomerController::class, 'step1'])->name('step1');
        Route::post('/step1', [CustomerController::class, 'saveStep1'])->name('step1.save');
        Route::get('/step2', [CustomerController::class, 'step2'])->name('step2');
        Route::post('/step2', [CustomerController::class, 'saveStep2'])->name('step2.save');
        Route::get('/step3', [CustomerController::class, 'step3'])->name('step3');
        Route::post('/step3', [CustomerController::class, 'saveStep3'])->name('step3.save');
        Route::get('/step4', [CustomerController::class, 'step4'])->name('step4');
        Route::get('/step5', [CustomerController::class, 'step5'])->name('step5');
        Route::post('/step5/upload', [CustomerController::class, 'uploadDocument'])->name('step5.upload');
        Route::get('/step6', [CustomerController::class, 'step6'])->name('step6');
        Route::post('/step6', [CustomerController::class, 'saveStep6'])->name('step6.save');
        Route::get('/step7', [CustomerController::class, 'step7'])->name('step7');
        Route::post('/step7', [CustomerController::class, 'saveStep7'])->name('step7.save');
        Route::get('/step8', [CustomerController::class, 'step8'])->name('step8');
        Route::post('/application/submit', [CustomerController::class, 'submit'])->name('application.submit');
        Route::get('/application/{id}/resume', [CustomerController::class, 'resumeDraft'])->name('application.resume');
    });

    // Lease Application Workflow Routes (Process Flow 6.2-6.4)
    Route::middleware('permission:application.edit')->group(function () {
        Route::post('/application/{id}/validate', [CustomerController::class, 'validateApplication'])->name('application.validate');
        Route::post('/application/{id}/approve', [CustomerController::class, 'approveApplication'])->name('application.approve');
        Route::post('/application/{id}/reject', [CustomerController::class, 'rejectApplication'])->name('application.reject');
        Route::post('/application/{id}/move-to-mining', [CustomerController::class, 'moveToMining'])->name('application.moveToMining');
        Route::post('/application/document/{id}/status', [CustomerController::class, 'updateDocumentStatus'])->name('application.document.status');
    });
    Route::middleware('permission:application.view')->group(function () {
        Route::get('/application/{id}/report', [CustomerController::class, 'generateReport'])->name('application.report');
    });

    // Mining Plan routes
    Route::middleware('permission:mining.view')->group(function () {
        Route::get('/miningplan', [MiningController::class, 'index'])->name('miningplan.index');
        Route::get('/projectfolder', [MiningController::class, 'projectFolder'])->name('projectfolder');
        Route::get('/document', [MiningController::class, 'Document'])->name('document');
        Route::get('/process', [MiningController::class, 'Process'])->name('process');
    });
    Route::middleware('permission:mining.create')->group(function () {
        Route::get('/newapplication', [MiningController::class, 'newApplication'])->name('newapplication');
        Route::post('/newapplication', [MiningController::class, 'store'])->name('newapplication.store');
        Route::post('/mining/document/upload', [MiningController::class, 'uploadDocument'])->name('mining.document.upload');
    });
    Route::middleware('permission:mining.edit')->group(function () {
        Route::post('/mining/document/{id}/validate', [MiningController::class, 'validateDocument'])->name('mining.document.validate');
        Route::post('/mining/application/{id}/stage', [MiningController::class, 'advanceStage'])->name('mining.application.stage');
        Route::post('/mining/application/{id}/move-to-environment', [MiningController::class, 'moveToEnvironment'])->name('mining.application.moveToEnvironment');
    });

    // ─── Environment Clearance — Unified Routes ───────────────────────────
    Route::middleware('permission:environment.view')->group(function () {
        // Unified Landing Page (B1 + B2 all projects)
        Route::get('/eviron', [EnverionsoneController::class, 'index'])->name('eviron.index');

        // Unified Project Show (dynamic folder tabs — SC1 / SC2 / B2)
        Route::get('/eviron/{id}', [EnverionsoneController::class, 'show'])->whereNumber('id')->name('eviron.show');

        // Create Wizard — Step 1: Category Selection, Step 2: Project Details
        Route::get('/eviron/create', [EnverionsoneController::class, 'create'])->name('eviron.create');

        // Backward-compatible routes (still accessible, now redirect to show page)
        Route::get('/environstage1', [EnverionsoneController::class, 'index1'])->name('environstage1');
        Route::get('/environstage2', [EnverionsoneController::class, 'index2'])->name('environstage2');

        // B2 workflow — list & detail (kept for backward compat)
        Route::get('/environment-b2', [EnvironmentalB2Controller::class, 'index'])->name('environment-b2.index');
        Route::get('/environment-b2/step/{step}', [EnvironmentalB2Controller::class, 'wizard'])->whereNumber('step')->name('environment-b2.step');
        Route::get('/environment-b2/{project}', [EnvironmentalB2Controller::class, 'show'])->name('environment-b2.show');

        // Document download (shared between B1 & B2)
        Route::get('/environment-b2/documents/{document}/download', [EnvironmentalB2Controller::class, 'download'])->name('environment-b2.documents.download');
        Route::get('/eviron/documents/{document}/download', [EnverionsoneController::class, 'downloadDocument'])->name('eviron.documents.download');
    });

    Route::middleware('permission:environment.b2.create')->group(function () {
        // Unified create (handles B1-SC1, B1-SC2, B2)
        Route::post('/eviron', [EnverionsoneController::class, 'store'])->name('eviron.store');

        // B2 legacy store
        Route::post('/environment-b2', [EnvironmentalB2Controller::class, 'store'])->name('environment-b2.store');
    });

    Route::middleware('permission:environment.b2.upload')->group(function () {
        // Unified document upload (for B1 & B2 via eviron/{id})
        Route::post('/eviron/{id}/documents/{document}/upload', [EnverionsoneController::class, 'uploadDocument'])->name('eviron.documents.upload');
        Route::post('/eviron/{id}/documents/add', [EnverionsoneController::class, 'addDocument'])->name('eviron.documents.add');

        // B2 legacy upload
        Route::post('/environment-b2/documents/{document}/upload', [EnvironmentalB2Controller::class, 'upload'])->name('environment-b2.documents.upload');
    });

    Route::middleware('permission:environment.b2.review')->group(function () {
        // Unified project status update
        Route::post('/eviron/{id}/status', [EnverionsoneController::class, 'updateStatus'])->name('eviron.status');
        Route::post('/eviron/{id}/submit-sc1-ppt', [EnverionsoneController::class, 'submitSc1ToPpt'])->name('eviron.submitSc1ToPpt');
        Route::post('/eviron/{id}/submit-sc2-ppt', [EnverionsoneController::class, 'submitSc2ToPpt'])->name('eviron.submitSc2ToPpt');

        // Unified document review
        Route::post('/eviron/{id}/documents/{document}/review', [EnverionsoneController::class, 'reviewDocument'])->name('eviron.documents.review');

        // B2 legacy review
        Route::post('/environment-b2/{project}/status', [EnvironmentalB2Controller::class, 'updateStatus'])->name('environment-b2.status');
        Route::post('/environment-b2/documents/{document}/review', [EnvironmentalB2Controller::class, 'review'])->name('environment-b2.documents.review');
    });


    // EC Certificate Issuance
    Route::middleware('permission:environment.view')->group(function () {
        Route::get('/ec-certificate', [EcCertificateController::class, 'index'])->name('ec-certificate.index');
        Route::get('/ec-certificate/{id}', [EcCertificateController::class, 'show'])->whereNumber('id')->name('ec-certificate.show');
        Route::get('/ec-certificate/step/{step}', [EcCertificateController::class, 'wizard'])->whereNumber('step')->name('ec-certificate.step');
        Route::post('/ec-certificate/step/{step}', [EcCertificateController::class, 'saveStep'])->whereNumber('step')->name('ec-certificate.saveStep');
        Route::post('/ec-certificate', [EcCertificateController::class, 'store'])->name('ec-certificate.store');
    });


    // PPT Department
    Route::middleware('permission:ppt.view')->group(function () {
        Route::get('/ppt-department', [PptDepartmentController::class, 'index'])->name('ppt-department.index');
        Route::get('/ppt-department/step/{step}', [PptDepartmentController::class, 'wizard'])->whereNumber('step')->name('ppt-department.step');
        Route::post('/ppt-department/step/{step}', [PptDepartmentController::class, 'saveStep'])->whereNumber('step')->name('ppt-department.saveStep');
        Route::post('/ppt-department', [PptDepartmentController::class, 'store'])->name('ppt-department.store');
        Route::get('/ppt-department/{id}', [PptDepartmentController::class, 'show'])->whereNumber('id')->name('ppt-department.show');
        Route::post('/ppt-department/{id}/approve-stage', [PptDepartmentController::class, 'approvePresentation'])->whereNumber('id')->name('ppt-department.approveStage');
        Route::post('/ppt-department/upload', [PptDepartmentController::class, 'uploadDocument'])->name('ppt-department.upload');
    });

    // DGPS Survey
    Route::middleware('permission:dgps.view')->group(function () {
        Route::get('/dgps-survey', [DgpsSurveyController::class, 'index'])->name('dgps-survey.index');
        Route::get('/dgps-survey/step/{step}', [DgpsSurveyController::class, 'wizard'])->whereNumber('step')->name('dgps-survey.step');
        Route::post('/dgps-survey/step/{step}', [DgpsSurveyController::class, 'saveStep'])->whereNumber('step')->name('dgps-survey.saveStep');
        Route::post('/dgps-survey', [DgpsSurveyController::class, 'store'])->name('dgps-survey.store');
        Route::get('/dgps-survey/{id}', [DgpsSurveyController::class, 'show'])->whereNumber('id')->name('dgps-survey.show');
        Route::post('/dgps-survey/upload', [DgpsSurveyController::class, 'uploadDocument'])->name('dgps-survey.upload');
    });

    // Drone Survey
    Route::middleware('permission:drone.view')->group(function () {
        Route::get('/drone-survey', [DroneSurveyController::class, 'index'])->name('drone-survey.index');
        Route::get('/drone-survey/step/{step}', [DroneSurveyController::class, 'wizard'])->whereNumber('step')->name('drone-survey.step');
    });

    // EC Compliance (Half Yearly Compliance)
    Route::middleware('permission:environment.view')->group(function () {
        Route::get('/ec-compliance', [EcComplianceController::class, 'index'])->name('ec-compliance.index');
        Route::get('/ec-compliance/step/{step}', [EcComplianceController::class, 'wizard'])->whereNumber('step')->name('ec-compliance.step');
        Route::post('/ec-compliance/step/{step}', [EcComplianceController::class, 'saveStep'])->whereNumber('step')->name('ec-compliance.saveStep');
        Route::post('/ec-compliance', [EcComplianceController::class, 'store'])->name('ec-compliance.store');
        Route::get('/ec-compliance/{id}', [EcComplianceController::class, 'show'])->whereNumber('id')->name('ec-compliance.show');
        Route::post('/ec-compliance/upload', [EcComplianceController::class, 'uploadDocument'])->name('ec-compliance.upload');
    });

});

