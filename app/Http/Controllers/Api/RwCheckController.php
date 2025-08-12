<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rw;
use Illuminate\Http\Request;

class RwCheckController extends Controller
{
    public function checkRw(Request $request)
    {
        $number = $request->input('number');
        if (!$number) {
            return response()->json(['is_taken' => false]);
        }
        
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        $isTaken = Rw::where('number', $paddedNumber)->exists();

        return response()->json([
            'is_taken' => $isTaken,
        ]);
    }
}