<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Report;
use App\Notifications\NewCommentOnReportNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Report $report)
    {
        $this->authorize('create', [Comment::class, $report]);

        $request->validate(['body' => 'required|string|max:2000']);

        $comment = $report->comments()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        if ($report->resident->user_id !== auth()->id()) {
            $report->resident->user->notify(new NewCommentOnReportNotification($comment));
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}