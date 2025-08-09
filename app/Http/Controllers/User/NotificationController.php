<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menampilkan halaman daftar notifikasi untuk pengguna yang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil semua notifikasi milik pengguna, urutkan dari yang terbaru
        $notifications = $user->notifications()->latest()->paginate(10);

        // Baris untuk menandai semua sudah dibaca telah dihapus dari sini.

        return view('pages.app.notifications.index', compact('notifications'));
    }

    /**
     * Menandai notifikasi sebagai telah dibaca dan mengarahkan ke laporan.
     */
    public function read(DatabaseNotification $notification)
    {
        // Pastikan notifikasi ini milik user yang sedang login (untuk keamanan)
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengakses notifikasi ini.');
        }

        // Tandai sebagai telah dibaca
        $notification->markAsRead();

        // Arahkan ke halaman detail laporan yang sesuai
        return redirect()->route('report.show', $notification->data['report_code']);
    }
}