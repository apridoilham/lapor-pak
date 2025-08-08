<?php

namespace App\Listeners;

use App\Events\ReportStatusUpdated;
use App\Mail\ReportStatusUpdatedMail; // Kita akan buat ini di langkah berikutnya
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

// Implement ShouldQueue agar pengiriman email berjalan di background
class SendReportStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReportStatusUpdated $event): void
    {
        // Ambil pengguna dari laporan yang ada di dalam event
        $user = $event->report->resident->user;

        // Kirim email
        Mail::to($user)->send(new ReportStatusUpdatedMail($event->report));
    }
}