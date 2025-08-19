<?php

use Illuminate\Support\Facades\Route;

// Controller untuk Autentikasi
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Controller untuk Pengguna (Warga)
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\CommentController;
use App\Http\Controllers\User\NotificationController;

// Controller untuk Admin Panel
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
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;

// --- Rute Autentikasi (Publik) ---
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('auth/google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::post('logout', [GoogleController::class, 'logout'])->name('logout')->middleware('auth');

// --- Rute Pengguna (Wajib Login & Berperan Resident) ---
Route::middleware(['auth', 'role:resident', 'profile.completed'])->group(function () {
    // ... Rute pengguna lainnya tetap sama ...
});

// --- Rute Panel Admin (Wajib Login & Berperan Admin atau Super Admin) ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
    Route::get('/notifications/{notification}/read', [AdminNotificationController::class, 'readAndRedirect'])->name('notifications.read');

    // Profil Admin
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
    
    // Manajemen Pelapor
    Route::get('/residents/{resident}/reports-for-alert', [ResidentController::class, 'getReportsForDeletionAlert'])->name('residents.reports_for_alert');
    
    // [PERBAIKAN] Menambahkan 'destroy' ke dalam daftar pengecualian
    Route::resource('/resident', ResidentController::class)->except([
        'create', 'store', 'edit', 'update', 'destroy'
    ]);
    
    // Manajemen Laporan
    Route::get('/export-reports', [ReportExportController::class, 'create'])->name('report.export.create');
    Route::post('/export-reports', [ReportExportController::class, 'store'])->name('report.export.store');
    Route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    Route::resource('/report-status', ReportStatusController::class)->except('create', 'index', 'show');
    Route::resource('/report', ReportController::class);

    // Log Aktivitas
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Rute Khusus Super Admin
    Route::middleware(['role:super-admin'])->group(function () {
        Route::resource('/report-category', ReportCategoryController::class);
        Route::resource('/admin-user', AdminUserController::class);
        Route::resource('/rtrw', RtRwController::class)->except(['show']);
    });
});