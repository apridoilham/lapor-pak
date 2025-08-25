<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReportStatusUpdatedNotification extends Notification implements ShouldBroadcast
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
        return ['database', 'broadcast'];
    }

    public function broadcastAs(): string
    {
        return 'report.status.updated';
    }

    private function generateMessage(): string
    {
        $reportTitle = \Str::limit($this->report->title, 20);
        $statusLabel = $this->report->latestStatus->status->label();

        if (isset($this->changes['status'])) {
            return 'Status pada laporan Anda (' . $reportTitle . ') diubah dari <strong>' . $this->changes['status']['from'] . '</strong> menjadi <strong>' . $this->changes['status']['to'] . '</strong>.';
        } elseif (isset($this->changes['description_updated'])) {
            return 'Catatan pada progress laporan (' . $reportTitle . ') dengan status <strong>' . $statusLabel . '</strong> telah diperbarui oleh admin.';
        } elseif (isset($this->changes['image_updated'])) {
            return 'Admin menambahkan gambar bukti baru pada progress laporan (' . $reportTitle . ') dengan status <strong>' . $statusLabel . '</strong>.';
        }

        return 'Status laporan Anda (' . $reportTitle . ') diperbarui menjadi <strong>' . $statusLabel . '</strong>.';
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'status_update',
            'report_id' => $this->report->id,
            'report_code' => $this->report->code,
            'status_message' => $this->report->latestStatus->status->label(),
            'changes' => $this->changes,
            'message' => $this->generateMessage(),
            'action_by_user_id' => $this->actorId,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => $this->generateMessage(),
            'report_code' => $this->report->code,
        ]);
    }
}