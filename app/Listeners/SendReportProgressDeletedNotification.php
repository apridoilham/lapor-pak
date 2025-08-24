<?php

namespace App\Listeners;

use App\Events\ReportProgressDeleted;
use App\Notifications\ReportProgressDeletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReportProgressDeletedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(ReportProgressDeleted $event): void
    {
        $user = $event->report->resident->user;
        $user->notify(new ReportProgressDeletedNotification($event->report->id, $event->actorId, $event->deletedStatusLabel));
    }
}