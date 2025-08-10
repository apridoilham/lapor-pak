<?php

namespace App\Providers;

use App\Events\ReportStatusUpdated;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\SendReportStatusNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
        ],
        ReportStatusUpdated::class => [
            SendReportStatusNotification::class,
        ],
    ];

    public function boot(): void
    {
        
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}