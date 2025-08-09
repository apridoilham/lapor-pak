<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Super Admin melihat semua log, Admin biasa hanya melihat log miliknya
        if ($user->hasRole('super-admin')) {
            $activities = LoginActivity::with('user')->latest('login_at')->paginate(20);
        } else {
            $activities = $user->loginActivities()->latest('login_at')->paginate(20);
        }

        return view('pages.admin.activity.index', compact('activities'));
    }
}