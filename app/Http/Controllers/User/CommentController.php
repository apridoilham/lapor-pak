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

        // Kirim notifikasi jika yang berkomentar bukan pemilik laporan
        if ($report->resident->user_id !== auth()->id()) {
            $report->resident->user->notify(new NewCommentOnReportNotification($comment));
        }

        // ▼▼▼ PERUBAHAN UTAMA DI SINI ▼▼▼
        // Jika permintaan datang dari JavaScript (AJAX)
        if ($request->wantsJson()) {
            // Muat relasi user agar kita bisa menampilkan nama & avatar di frontend
            $comment->load('user.resident');
            
            // Kirim kembali data komentar baru sebagai respons JSON
            return response()->json($comment, 201); // 201 = Created
        }

        // Jika ini adalah request biasa (non-AJAX), kembalikan ke halaman sebelumnya
        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}