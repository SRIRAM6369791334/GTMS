<?php

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

Route::get('/', function () {
    return view('pages.login');
});

Route::get('/dashboard', function () {
    return view('pages.index');
});



// user routes

Route::resource('/user',UserController::class)->only('index');
Route::post('useradd', [UserController::class, 'store'])->name('useradd');
Route::post('useredit', [UserController::class, 'update'])->name('useredit');
Route::post('userdelete', [UserController::class, 'destroy'])->name('userdelete');


// role routes
Route::resource('/roles', RolesController::class)->only('index');
Route::post('roleadd', [RolesController::class, 'store'])->name('roleadd');
Route::post('roleupdate', [RolesController::class, 'update'])->name('roleupdate');
Route::post('roledelete', [RolesController::class, 'destroy'])->name('roledelete');

// Branch routes
Route::resource('/branch', BranchController::class)->only('index');
Route::post('branchadd', [BranchController::class, 'store'])->name('branchadd');
Route::post('branchedit',[BranchController::class, 'update'])->name('branchedit');
Route::post('branchdelete', [BranchController::class, 'destroy'])->name('branchdelete');

// Category routes
Route::resource('/category', CategoryController::class)->only('index');
Route::post('categoryadd', [CategoryController::class, 'store'])->name('categoryadd');
Route::post('categoryedit', [CategoryController::class, 'update'])->name('categoryedit');
Route::post('categorydelete', [CategoryController::class, 'destroy'])->name('categorydelete');

// unit routes
Route::resource('/unit', UnitController::class)->only('index');

// Product routes
Route::resource('/product', ProductController::class)->only('index');
Route::post('productadd', [ProductController::class, 'store'])->name('productadd');

// Product Stock routes
Route::resource('/productstock', ProductStockController::class)->only('index');


// Customer routes
Route::resource('/application', CustomerController::class)->only('index');
Route::get('/step1', [CustomerController::class, 'step1'])->name('step1');
Route::get('/step2', [CustomerController::class, 'step2'])->name('step2');
Route::get('/step3', [CustomerController::class, 'step3'])->name('step3');
Route::get('/step4', [CustomerController::class, 'step4'])->name('step4');
Route::get('/step5', [CustomerController::class, 'step5'])->name('step5');
Route::get('/step6', [CustomerController::class, 'step6'])->name('step6');
Route::get('/step7', [CustomerController::class, 'step7'])->name('step7');
Route::get('/viewapplication', [CustomerController::class, 'viewApplication'])->name('viewapplication');

// mining plane


Route::resource('/miningplan', MiningController::class)->only('index');
Route::get('/newapplication', [MiningController::class, 'newApplication'])->name('newapplication');
Route::get('/projectfolder', [MiningController::class, 'projectFolder'])->name('projectfolder');
Route::get('/document', [MiningController::class, 'Document'])->name('document');
Route::get('/process', [MiningController::class, 'Process'])->name('process');


// environstage 1
Route::resource('eviron', EnverionsoneController::class)->only('index');
// Route::get('/environstage1', [EnverionsoneController::class, 'index'])->name('environstage1');
Route::get('/environstage1', [EnverionsoneController::class, 'index1'])->name('environstage1');
Route::get('/environstage2', [EnverionsoneController::class, 'index2'])->name('environstage2');

// Environment Clearance — B2 workflow
Route::get('/environment-b2', [EnvironmentalB2Controller::class, 'index'])->name('environment-b2.index');
Route::get('/environment-b2/step/{step}', [EnvironmentalB2Controller::class, 'wizard'])->whereNumber('step')->name('environment-b2.step');
Route::post('/environment-b2', [EnvironmentalB2Controller::class, 'store'])->name('environment-b2.store');
Route::get('/environment-b2/{project}', [EnvironmentalB2Controller::class, 'show'])->name('environment-b2.show');
Route::post('/environment-b2/{project}/status', [EnvironmentalB2Controller::class, 'updateStatus'])->name('environment-b2.status');
Route::post('/environment-b2/documents/{document}/upload', [EnvironmentalB2Controller::class, 'upload'])->name('environment-b2.documents.upload');
Route::post('/environment-b2/documents/{document}/review', [EnvironmentalB2Controller::class, 'review'])->name('environment-b2.documents.review');
Route::get('/environment-b2/documents/{document}/download', [EnvironmentalB2Controller::class, 'download'])->name('environment-b2.documents.download');

// PPT Department — UI workflow
Route::get('/ppt-department', [PptDepartmentController::class, 'index'])->name('ppt-department.index');
Route::get('/ppt-department/step/{step}', [PptDepartmentController::class, 'wizard'])->whereNumber('step')->name('ppt-department.step');

// EC Certificate Issuance — UI workflow
Route::get('/ec-certificate', [EcCertificateController::class, 'index'])->name('ec-certificate.index');
Route::get('/ec-certificate/step/{step}', [EcCertificateController::class, 'wizard'])->whereNumber('step')->name('ec-certificate.step');

// DGPS Survey — UI workflow
Route::get('/dgps-survey', [DgpsSurveyController::class, 'index'])->name('dgps-survey.index');
Route::get('/dgps-survey/step/{step}', [DgpsSurveyController::class, 'wizard'])->whereNumber('step')->name('dgps-survey.step');

// Drone Survey — UI workflow
Route::get('/drone-survey', [DroneSurveyController::class, 'index'])->name('drone-survey.index');
Route::get('/drone-survey/step/{step}', [DroneSurveyController::class, 'wizard'])->whereNumber('step')->name('drone-survey.step');
