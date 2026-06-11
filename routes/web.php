<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Customer\CustomerCreateController;
use App\Http\Controllers\Customer\CustomerImportController;
use App\Http\Controllers\Customer\CustomerExportController;
use App\Http\Controllers\Configuration\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Logs\LogsController;

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('no.cache');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::middleware(['auth.web', 'no.cache'])->group(function () {
    Route::get('/test-admin', function () {
        return 'Admin Access Granted';
    })->middleware('admin.only');
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

    Route::get('/api/dashboard/summary', [DashboardController::class, 'summary']);

    Route::middleware('admin.only')->group(function() {
        Route::get('/logs/customer-activity', [LogsController::class, 'customerActivityPage']);
        Route::get('/api/logs/customer-activity', [LogsController::class, 'customerActivityData']);

        Route::get('/logs/export-history', [LogsController::class, 'exportHistoryPage']);
        Route::get('/api/logs/export-history', [LogsController::class, 'exportHistoryData']);

        Route::get('/logs/system-logs', [LogsController::class, 'systemLogsPage']);
        Route::get('/api/logs/system-logs', [LogsController::class, 'systemLogsData']);

        Route::get('/api/configuration', [ConfigurationController::class, 'index']);
        Route::post('/configuration/update', [ConfigurationController::class, 'update']);
        Route::get('/configuration', function () {
            if (request()->ajax()) {
                return view('configuration.content');
            }
            return view('configuration.index');
        });
    });

    
});

