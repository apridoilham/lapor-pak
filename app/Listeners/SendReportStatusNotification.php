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
        $owner = $event->report->resident->user;

        if ($owner->id === $event->actorId) {
            return;
        }

        $owner->notify(new ReportStatusUpdatedNotification($event->report, $event->actorId, $event->changes));
    }
}