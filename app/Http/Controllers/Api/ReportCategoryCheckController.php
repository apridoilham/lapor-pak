<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class ReportCategoryCheckController extends Controller
{
    public function checkName(Request $request)
    {
        $name = $request->input('name');
        if (!$name) {
            return response()->json(['is_taken' => false]);
        }

        $isTaken = ReportCategory::where('name', $name)->exists();

        return response()->json([
            'is_taken' => $isTaken,
        ]);
    }
}