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
        $ignoreId = $request->input('ignore_rw_id');

        if (!$number) {
            return response()->json(['is_taken' => false]);
        }
        
        $paddedNumber = str_pad($number, 2, '0', STR_PAD_LEFT);

        $query = Rw::where('number', $paddedNumber);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $isTaken = $query->exists();

        return response()->json([
            'is_taken' => $isTaken,
        ]);
    }
}