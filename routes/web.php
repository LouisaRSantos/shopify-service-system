<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Customer\CustomerCreateController;
use App\Http\Controllers\Customer\CustomerImportController;
use App\Http\Controllers\Customer\CustomerExportController;

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('no.cache');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware(['auth.web', 'no.cache'])->group(function () {
    Route::get('/', function () {
        if (request()->ajax()) {
            return view('dashboard.content');
        }
        return view('dashboard.index');
    });

    Route::get('/customers', function () {
        if (request()->ajax()) {
            return view('customers.content');
        }
        return view('customers.index');
    });

    Route::get('/customers/create', [CustomerCreateController::class, 'index']);
    Route::post('/customers/store', [CustomerCreateController::class, 'store']);
    Route::get('/customers/import', [CustomerImportController::class, 'index']);
    Route::get('/customers/import/template', [CustomerImportController::class, 'downloadTemplate']);
    Route::post('/customers/import/process', [CustomerImportController::class, 'process']);

    Route::get('/customers/export', [CustomerExportController::class, 'index']);
    Route::post('/customers/export/start', [CustomerExportController::class, 'start']);
    Route::get('/customers/export/status', [CustomerExportController::class, 'status']);
    Route::get('/customers/export/download', [CustomerExportController::class, 'download']);
});

