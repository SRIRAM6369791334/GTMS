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

use App\Http\Controllers\CustomerDirectoryController;

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
        Route::post('/application/submit', [CustomerController::class, 'submit'])->name('application.submit');
        Route::get('/application/{id}/resume', [CustomerController::class, 'resumeDraft'])->name('application.resume');
    });

    // Lease Application Workflow Routes (Process Flow 6.2-6.4)
    Route::middleware('permission:application.view')->group(function () {
        Route::post('/application/{id}/validate', [CustomerController::class, 'validateApplication'])->name('application.validate');
        Route::post('/application/{id}/approve', [CustomerController::class, 'approveApplication'])->name('application.approve');
        Route::post('/application/{id}/reject', [CustomerController::class, 'rejectApplication'])->name('application.reject');
        Route::post('/application/document/{id}/status', [CustomerController::class, 'updateDocumentStatus'])->name('application.document.status');
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
    });

    // Environment Clearance routes
    Route::middleware('permission:environment.view')->group(function () {
        Route::get('/eviron', [EnverionsoneController::class, 'index'])->name('eviron.index');
        Route::get('/environstage1', [EnverionsoneController::class, 'index1'])->name('environstage1');
        Route::get('/environstage2', [EnverionsoneController::class, 'index2'])->name('environstage2');

        // B2 workflow
        Route::get('/environment-b2', [EnvironmentalB2Controller::class, 'index'])->name('environment-b2.index');
        Route::get('/environment-b2/step/{step}', [EnvironmentalB2Controller::class, 'wizard'])->whereNumber('step')->name('environment-b2.step');
        Route::get('/environment-b2/{project}', [EnvironmentalB2Controller::class, 'show'])->name('environment-b2.show');
    });

    Route::middleware('permission:environment.b2.create')->group(function () {
        Route::post('/environment-b2', [EnvironmentalB2Controller::class, 'store'])->name('environment-b2.store');
    });

    Route::middleware('permission:environment.b2.review')->group(function () {
        Route::post('/environment-b2/{project}/status', [EnvironmentalB2Controller::class, 'updateStatus'])->name('environment-b2.status');
    });

    Route::middleware('permission:environment.b2.upload')->group(function () {
        Route::post('/environment-b2/documents/{document}/upload', [EnvironmentalB2Controller::class, 'upload'])->name('environment-b2.documents.upload');
    });

    Route::middleware('permission:environment.b2.review')->group(function () {
        Route::post('/environment-b2/documents/{document}/review', [EnvironmentalB2Controller::class, 'review'])->name('environment-b2.documents.review');
    });

    Route::middleware('permission:environment.view')->group(function () {
        Route::get('/environment-b2/documents/{document}/download', [EnvironmentalB2Controller::class, 'download'])->name('environment-b2.documents.download');
    });


    // EC Certificate Issuance
    Route::middleware('permission:environment.view')->group(function () {
        Route::get('/ec-certificate', [EcCertificateController::class, 'index'])->name('ec-certificate.index');
        Route::get('/ec-certificate/step/{step}', [EcCertificateController::class, 'wizard'])->whereNumber('step')->name('ec-certificate.step');
    });


    // PPT Department
    Route::middleware('permission:ppt.view')->group(function () {
        Route::get('/ppt-department', [PptDepartmentController::class, 'index'])->name('ppt-department.index');
        Route::get('/ppt-department/step/{step}', [PptDepartmentController::class, 'wizard'])->whereNumber('step')->name('ppt-department.step');
    });

    // DGPS Survey
    Route::middleware('permission:dgps.view')->group(function () {
        Route::get('/dgps-survey', [DgpsSurveyController::class, 'index'])->name('dgps-survey.index');
        Route::get('/dgps-survey/step/{step}', [DgpsSurveyController::class, 'wizard'])->whereNumber('step')->name('dgps-survey.step');
    });

    // Drone Survey
    Route::middleware('permission:drone.view')->group(function () {
        Route::get('/drone-survey', [DroneSurveyController::class, 'index'])->name('drone-survey.index');
        Route::get('/drone-survey/step/{step}', [DroneSurveyController::class, 'wizard'])->whereNumber('step')->name('drone-survey.step');
    });

});

