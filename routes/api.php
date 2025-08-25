<?php

use App\Http\Controllers\Api\DependentDropdownController;
use App\Http\Controllers\Api\EmailCheckController;
use App\Http\Controllers\Api\ReportCategoryCheckController;
use App\Http\Controllers\Api\RwCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/get-rts-by-rw/{rwId}', [DependentDropdownController::class, 'getRtsByRw']);
Route::post('/check-rw', [RwCheckController::class, 'checkRw']);
Route::post('/check-report-category', [ReportCategoryCheckController::class, 'checkName']);
Route::post('/check-email', [EmailCheckController::class, 'checkEmail']);