<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailCheckController extends Controller
{
    public function checkEmail(Request $request)
    {
        $email = strtolower($request->input('email_username')) . '@bsblapor.com';
        $userIdToIgnore = $request->input('ignore_user_id');

        $query = User::where('email', $email);

        if ($userIdToIgnore) {
            $query->where('id', '!=', $userIdToIgnore);
        }

        return response()->json([
            'is_taken' => $query->exists(),
        ]);
    }
}