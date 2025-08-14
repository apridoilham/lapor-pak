<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil semua notifikasi, jangan di-paginate agar bisa di-filter di view
        $notifications = $user->notifications()->latest()->get(); 
        
        return view('pages.app.notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengakses notifikasi ini.');
        }

        $notification->markAsRead();

        $baseUrl = route('report.show', ['code' => $notification->data['report_code'], '_ref' => route('notifications.index')]);
        $fragment = '#komentar';

        if (isset($notification->data['type']) && $notification->data['type'] === 'status_update') {
            $fragment = '#riwayat-perkembangan';
        }

        return redirect($baseUrl . $fragment);
    }

    // ▼▼▼ METHOD BARU YANG MEMPERBAIKI ERROR ▼▼▼
    public function markSelectedAsRead(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);

        Auth::user()->notifications()
            ->whereIn('id', $request->ids)
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifikasi berhasil ditandai sebagai sudah dibaca.']);
    }

    // ▼▼▼ METHOD BARU YANG MEMPERBAIKI ERROR ▼▼▼
    public function deleteSelected(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:notifications,id',
        ]);
        
        Auth::user()->notifications()
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json(['message' => 'Notifikasi yang dipilih berhasil dihapus.']);
    }
}