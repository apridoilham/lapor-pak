<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportProgressDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $reportId;
    public ?int $actorId;
    public string $deletedStatusLabel;

    public function __construct(int $reportId, ?int $actorId, string $deletedStatusLabel)
    {
        $this->reportId = $reportId;
        $this->actorId = $actorId;
        $this->deletedStatusLabel = $deletedStatusLabel;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $report = Report::find($this->reportId);
        if (!$report) {
            return [];
        }

        return [
            'type' => 'progress_deleted',
            'report_id' => $report->id,
            'report_code' => $report->code,
            'message' => 'Progress dengan status <strong>' . $this->deletedStatusLabel . '</strong> pada laporan Anda (' . \Str::limit($report->title, 20) . ') telah dihapus oleh admin.',
            'action_by_user_id' => $this->actorId,
        ];
    }
}