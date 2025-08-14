<?php

namespace App\Listeners;

use App\Events\ReportStatusUpdated;
use App\Notifications\ReportStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReportStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(ReportStatusUpdated $event): void
    {
        $user = $event->report->resident->user;

        // Salurkan actorId ke Notification
        $user->notify(new ReportStatusUpdatedNotification($event->report, $event->actorId));
    }
}