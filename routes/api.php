<?php

use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Api\AdminUserCheckController;
use App\Http\Controllers\Api\DependentDropdownController;
use App\Http\Controllers\Api\EmailCheckController;
use App\Http\Controllers\Api\ReportCategoryCheckController;
use App\Http\Controllers\Api\RWCheckController;
use Illuminate\Support\Facades\Route;

Route::post('/check-admin-email', [AdminUserCheckController::class, 'checkEmail']);
Route::get('/get-rts-by-rw/{rwId}', [DependentDropdownController::class, 'getRtsByRw']);
Route::post('/check-rw', [RWCheckController::class, 'checkRw']);
Route::post('/check-report-category', [ReportCategoryCheckController::class, 'checkName']);
Route::post('/check-email', [EmailCheckController::class, 'checkEmail']);

Route::middleware(['auth:sanctum', 'role:admin|super-admin'])->prefix('admin')->group(function() {
    // Rute API untuk dashboard dihapus
    // Route::get('/dashboard-stats', [DashboardController::class, 'getDashboardStats']);
    Route::get('/residents/{resident}/reports-for-alert', [ResidentController::class, 'getReportsForDeletionAlert']);
});