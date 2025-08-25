<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get(); 

        return view('pages.app.notifications.index', compact('notifications'));
    }

    public function read(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengakses notifikasi ini.');
        }

        $notification->markAsRead();

        $reportCode = $notification->data['report_code'];
        $baseUrl = route('report.show', [
            'report' => $reportCode, 
            '_ref' => route('notifications.index')
        ]);
        
        $fragment = '#komentar';

        if (isset($notification->data['type']) && $notification->data['type'] === 'status_update') {
            $fragment = '#riwayat-perkembangan';
        }

        return redirect($baseUrl . $fragment);
    }

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