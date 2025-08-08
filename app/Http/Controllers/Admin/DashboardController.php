<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;                // <-- DITAMBAHKAN
use App\Models\ReportCategory;       // <-- DITAMBAHKAN
use App\Models\Resident;               // <-- DITAMBAHKAN
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * PERUBAHAN DI SINI:
     * Ambil semua data count di sini dan kirimkan ke view.
     */
    public function index()
    {
        $reportCategoryCount = ReportCategory::count();
        $reportCount = Report::count();
        $residentCount = Resident::count();

        return view('pages.admin.dashboard', compact(
            'reportCategoryCount',
            'reportCount',
            'residentCount'
        ));
    }
}