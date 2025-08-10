<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\ReportStatusController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\RtRwController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController as UserReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('/reports', [UserReportController::class, 'index'])->name('report.index');
Route::get('/report/{code}', [UserReportController::class, 'show'])->name('report.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/take-report', [UserReportController::class, 'take'])->name('report.take');
    Route::get('/preview', [UserReportController::class, 'preview'])->name('report.preview');
    Route::get('/create-report', [UserReportController::class, 'create'])->name('report.create');
    Route::post('/create-report', [UserReportController::class, 'store'])->name('report.store');
    Route::get('/report-success', [UserReportController::class, 'success'])->name('report.success');

    Route::get('/my-reports', [UserReportController::class, 'myReport'])->name('report.myreport');
    Route::get('/my-reports/{report}/edit', [UserReportController::class, 'edit'])->name('report.edit');
    Route::put('/my-reports/{report}', [UserReportController::class, 'update'])->name('report.update');
    Route::delete('/my-reports/{report}', [UserReportController::class, 'destroy'])->name('report.destroy');
    Route::get('/my-reports/{report}/complete', [UserReportController::class, 'showCompleteForm'])->name('report.complete.form');
    Route::post('/my-reports/{report}/complete', [UserReportController::class, 'complete'])->name('report.complete');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');

    Route::resource('/resident', ResidentController::class);
    Route::resource('/report-category', ReportCategoryController::class);
    Route::resource('/report', ReportController::class);

    Route::get('/export-reports', [ReportExportController::class, 'create'])->name('report.export.create');
    Route::post('/export-reports', [ReportExportController::class, 'store'])->name('report.export.store');

    Route::resource('/admin-user', AdminUserController::class)->middleware('role:super-admin');

    Route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    Route::resource('/report-status', ReportStatusController::class)->except('create');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    
    Route::get('/rtrw', [RtRwController::class, 'index'])->name('rtrw.index');
    Route::post('/rtrw', [RtRwController::class, 'store'])->name('rtrw.store');
    Route::delete('/rtrw/{rw}', [RtRwController::class, 'destroy'])->name('rtrw.destroy');
});