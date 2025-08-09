<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rw; // <-- Pastikan baris ini ada
use Illuminate\Http\Request;

class DependentDropdownController extends Controller
{
    public function getRtsByRw($rwId)
    {
        // Cari RW berdasarkan ID dan ambil semua RT yang berelasi
        $rw = Rw::with('rts')->find($rwId);

        if (!$rw) {
            return response()->json([], 404);
        }

        return response()->json($rw->rts);
    }
}