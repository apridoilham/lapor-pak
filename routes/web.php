<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\CommentController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Admin\ReportStatusController;
use App\Http\Controllers\Admin\ReportExportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\RtRwController;
use App\Http\Controllers\Api\AdminUserCheckController;
use App\Http\Controllers\Api\DependentDropdownController;
use App\Http\Controllers\Api\EmailCheckController;
use App\Http\Controllers\Api\ReportCategoryCheckController;
use App\Http\Controllers\Api\RwCheckController;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::prefix('api')->group(function () {
    Route::post('/check-admin-email', [AdminUserCheckController::class, 'checkEmail']);
    Route::get('/get-rts-by-rw/{rwId}', [DependentDropdownController::class, 'getRtsByRw']);
    Route::post('/check-rw', [RwCheckController::class, 'checkRw']);
    Route::post('/check-report-category', [ReportCategoryCheckController::class, 'checkName']);
    Route::post('/check-email', [EmailCheckController::class, 'checkEmail']);
});

Route::middleware(['auth', 'role:resident', 'profile.completed'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/reports', [UserReportController::class, 'index'])->name('report.index');
    Route::get('/report/{code}', [UserReportController::class, 'show'])->name('report.show');
    Route::get('/take-report', [UserReportController::class, 'take'])->name('report.take');
    Route::get('/preview', [UserReportController::class, 'preview'])->name('report.preview');
    Route::get('/create-report', [UserReportController::class, 'create'])->name('report.create');
    Route::post('/create-report', [UserReportController::class, 'store'])->name('report.store');
    Route::get('/report-success', [UserReportController::class, 'success'])->name('report.success');
    Route::get('/report-summary/{report:code}', [UserReportController::class, 'summary'])->name('report.summary');
    Route::get('/my-reports', [UserReportController::class, 'myReport'])->name('report.myreport');
    Route::get('/my-reports/{report}/edit', [UserReportController::class, 'edit'])->name('report.edit');
    Route::put('/my-reports/{report}', [UserReportController::class, 'update'])->name('report.update');
    Route::delete('/my-reports/{report}', [UserReportController::class, 'destroy'])->name('report.destroy');
    Route::get('/my-reports/{report}/complete', [UserReportController::class, 'showCompleteForm'])->name('report.complete.form');
    Route::post('/my-reports/{report}/complete', [UserReportController::class, 'complete'])->name('report.complete');
    Route::post('/report/{report}/comments', [CommentController::class, 'store'])->name('report.comments.store');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-selected-as-read', [NotificationController::class, 'markSelectedAsRead'])->name('notifications.read.selected');
    Route::post('/notifications/delete-selected', [NotificationController::class, 'deleteSelected'])->name('notifications.delete.selected');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::resource('/report', ReportController::class)->only(['index', 'show']);
    Route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    Route::resource('/report-status', ReportStatusController::class)->except('create', 'index', 'show');
    Route::get('/export-reports', [ReportExportController::class, 'create'])->name('report.export.create');
    Route::post('/export-reports', [ReportExportController::class, 'store'])->name('report.export.store');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    Route::resource('/resident', ResidentController::class)->except(['create', 'store', 'edit', 'update']);

    Route::middleware(['role:super-admin'])->group(function () {
        Route::resource('/report-category', ReportCategoryController::class);
        Route::resource('/admin-user', AdminUserController::class);
        Route::resource('/rtrw', RtRwController::class);
        Route::delete('/rt/{rt}', [RtRwController::class, 'destroyRt'])->name('rt.destroy');
    });
});