<?php

use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Api\DependentDropdownController;
use App\Http\Controllers\Api\EmailCheckController;
use App\Http\Controllers\Api\ReportCategoryCheckController;
use App\Http\Controllers\Api\RwCheckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/get-rts-by-rw/{rwId}', [DependentDropdownController::class, 'getRtsByRw']);
Route::post('/check-email', [EmailCheckController::class, 'checkEmail']);
Route::post('/check-rw', [RwCheckController::class, 'checkRw']);
Route::post('/check-report-category', [ReportCategoryCheckController::class, 'checkName']);

Route::middleware('auth:sanctum')->group(function() {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('role:admin|super-admin')->group(function () {
        Route::get('/residents/{resident}/reports-for-alert', [ResidentController::class, 'getReportsForDeletionAlert'])
             ->name('api.admin.residents.reports_for_alert');
    });

});