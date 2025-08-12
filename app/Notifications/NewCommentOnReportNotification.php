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
            'type' => 'new_comment',
            'report_id' => $this->comment->report->id,
            'report_code' => $this->comment->report->code,
            'comment_body' => \Str::limit($this->comment->body, 50),
            'action_by_user_id' => $this->comment->user_id,
        ];
    }
}