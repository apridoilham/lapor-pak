<?php

namespace App\Providers;

use App\Interfaces\AdminRepositoryInterface; // <-- DITAMBAHKAN
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Repositories\AdminRepository; // <-- DITAMBAHKAN
use App\Repositories\AuthRepository;
use App\Repositories\ResidentRepository;
use App\Repositories\ReportCategoryRepository;
use App\Repositories\ReportRepository;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Repositories\ReportStatusRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(ResidentRepositoryInterface::class, ResidentRepository::class);
        $this->app->bind(ReportCategoryRepositoryInterface::class, ReportCategoryRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(ReportStatusRepositoryInterface::class, ReportStatusRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class); // <-- DITAMBAHKAN
    }

    public function boot(): void
    {
        //
    }
}