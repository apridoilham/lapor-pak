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
        $notifications = $user->notifications()->latest()->paginate(10);
        
        $notifications->load('notifiable');

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

    public function destroy(DatabaseNotification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengakses notifikasi ini.');
        }

        $notification->delete();

        Swal::success('Berhasil', 'Notifikasi telah dihapus.');

        return back();
    }
}