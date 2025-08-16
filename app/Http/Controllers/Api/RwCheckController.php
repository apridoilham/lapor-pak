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
        // TAMBAHKAN BARIS INI untuk mengambil ID yang akan diabaikan
        $ignoreId = $request->input('ignore_rw_id');

        if (!$number) {
            return response()->json(['is_taken' => false]);
        }
        
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        // MODIFIKASI QUERY untuk mengabaikan ID tertentu
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