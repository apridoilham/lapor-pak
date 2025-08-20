<?php

use App\Http\Controllers\Admin\ResidentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin|super-admin'])->prefix('admin')->group(function() {
    Route::get('/residents/{resident}/reports-for-alert', [ResidentController::class, 'getReportsForDeletionAlert']);
});