<?php

use App\Http\Controllers\Api\DependentDropdownController; // Akan kita buat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/get-rts-by-rw/{rwId}', [DependentDropdownController::class, 'getRtsByRw']);