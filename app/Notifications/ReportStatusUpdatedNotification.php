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

    public Report $report;
    public ?int $actorId;

    public function __construct(Report $report, ?int $actorId)
    {
        $this->report = $report;
        $this->actorId = $actorId;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->report->latestStatus;

        return (new MailMessage)
                    ->subject('Update Status Laporan Anda: ' . $this->report->code)
                    ->greeting('Halo, ' . $notifiable->name . '.')
                    ->line('Ada pembaruan untuk laporan Anda dengan judul "' . $this->report->title . '".')
                    ->line('Status Terbaru: ' . $status->status->label())
                    ->line('Catatan: ' . $status->description)
                    ->action('Lihat Laporan', route('report.show', $this->report->code))
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'status_update',
            'report_id' => $this->report->id,
            'report_code' => $this->report->code,
            'status_message' => $this->report->latestStatus->status->value,
            'action_by_user_id' => $this->actorId,
        ];
    }
}