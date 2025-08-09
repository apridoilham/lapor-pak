<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        // ▼▼▼ KEMBALIKAN 3 BARIS INI ▼▼▼
        $reportCategoryCount = ReportCategory::count();
        $reportCount = Report::count();
        $residentCount = Resident::count();
        
        // Data untuk tabel laporan terbaru
        $latestReports = $this->reportRepository->getLatesReports();

        // Data untuk Pie Chart Kategori
        $categoriesWithCount = ReportCategory::withCount('reports')->get();
        $categoryLabels = $categoriesWithCount->pluck('name')->toArray();
        $categoryData = $categoriesWithCount->pluck('reports_count')->toArray();

        // Data untuk Bar Chart Laporan Harian
        $reportDaily = Report::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('count', 'date');

        $dailyLabels = [];
        $dailyData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            
            $dailyLabels[] = $date->isoFormat('D MMM');
            $dailyData[] = $reportDaily[$dateString] ?? 0;
        }

        // ▼▼▼ KEMBALIKAN VARIABEL STATISTIK KE DALAM compact() ▼▼▼
        return view('pages.admin.dashboard', compact(
            'reportCategoryCount',
            'reportCount',
            'residentCount',
            'latestReports',
            'categoryLabels',
            'categoryData',
            'dailyLabels',
            'dailyData'
        ));
    }
}