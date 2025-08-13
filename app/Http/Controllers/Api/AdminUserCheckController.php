<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserCheckController extends Controller
{
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'ignore_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $email = $request->input('email');
        $userIdToIgnore = $request->input('ignore_user_id');

        $query = User::where('email', $email);

        if ($userIdToIgnore) {
            $query->where('id', '!=', $userIdToIgnore);
        }

        $user = $query->first();

        if (!$user) {
            return response()->json(['is_taken' => false]);
        }

        $message = 'Email sudah terdaftar.';
        if ($user->hasRole('resident')) {
            $message = 'Email sudah terdaftar sebagai Pelapor, silakan hapus data Pelapor terlebih dahulu.';
        } elseif ($user->hasRole(['admin', 'super-admin'])) {
            $message = 'Email ini sudah terdaftar sebagai Admin.';
        }

        return response()->json([
            'is_taken' => true,
            'message' => $message,
        ]);
    }
}