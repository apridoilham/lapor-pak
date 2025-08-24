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
    public array $changes;

    public function __construct(Report $report, ?int $actorId, array $changes = [])
    {
        $this->report = $report;
        $this->actorId = $actorId;
        $this->changes = $changes;
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
        $reportTitle = \Str::limit($this->report->title, 20);
        $statusLabel = $this->report->latestStatus->status->label();
        $message = '';
        
        if (isset($this->changes['status'])) {
            $message = 'Status pada laporan Anda (' . $reportTitle . ') diubah dari <strong>' . $this->changes['status']['from'] . '</strong> menjadi <strong>' . $this->changes['status']['to'] . '</strong>.';
        } elseif (isset($this->changes['description_updated'])) {
            $message = 'Catatan pada progress laporan (' . $reportTitle . ') dengan status <strong>' . $statusLabel . '</strong> telah diperbarui oleh admin.';
        } elseif (isset($this->changes['image_updated'])) {
            $message = 'Admin menambahkan gambar bukti baru pada progress laporan (' . $reportTitle . ') dengan status <strong>' . $statusLabel . '</strong>.';
        } else {
            $message = 'Status laporan Anda (' . $reportTitle . ') diperbarui menjadi <strong>' . $statusLabel . '</strong>.';
        }

        return [
            'type' => 'status_update',
            'report_id' => $this->report->id,
            'report_code' => $this->report->code,
            'status_message' => $statusLabel,
            'changes' => $this->changes,
            'message' => $message,
            'action_by_user_id' => $this->actorId,
        ];
    }
}