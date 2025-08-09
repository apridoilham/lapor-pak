<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Kita akan mengirim notifikasi ini ke email dan database
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->report->latestStatus;

        return (new MailMessage)
                    ->subject('Update Status Laporan Anda: ' . $this->report->code)
                    ->greeting('Halo, ' . $notifiable->name . '.')
                    ->line('Ada pembaruan untuk laporan Anda dengan judul "' . $this->report->title . '".')
                    ->line('Status Terbaru: ' . $status->status->value)
                    ->line('Catatan: ' . $status->description)
                    ->action('Lihat Laporan', route('report.show', $this->report->code))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // Data ini yang akan disimpan di tabel notifications
        return [
            'report_id' => $this->report->id,
            'report_code' => $this->report->code,
            'title' => 'Status laporan ' . $this->report->code . ' telah diperbarui.',
            'message' => 'Status terbaru: ' . $this->report->latestStatus->status->value,
        ];
    }
}