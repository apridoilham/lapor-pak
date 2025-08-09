<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

        // Tandai semua notifikasi yang belum dibaca sebagai "sudah dibaca"
        $user->unreadNotifications->markAsRead();

        return view('pages.app.notifications.index', compact('notifications'));
    }
}