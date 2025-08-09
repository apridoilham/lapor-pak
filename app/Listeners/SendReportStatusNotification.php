<?php

namespace App\Listeners;

use App\Events\ReportStatusUpdated;
use App\Notifications\ReportStatusUpdatedNotification; // <-- Ganti Mailable dengan Notification
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

        // PERUBAHAN DI SINI:
        // Gunakan ->notify() untuk mengirim notifikasi
        $user->notify(new ReportStatusUpdatedNotification($event->report));
    }
}