<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentOnReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->comment->report->id,
            'report_code' => $this->comment->report->code,
            'title' => "{$this->comment->user->name} mengomentari laporan Anda ({$this->comment->report->code}).",
            'message' => "Komentar: " . \Str::limit($this->comment->body, 50),
        ];
    }
}