<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ResidentRepositoryInterface $residentRepository;

    public function __construct(ReportRepositoryInterface $reportRepository, ResidentRepositoryInterface $residentRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->residentRepository = $residentRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $rwId = $user->hasRole('super-admin') ? null : $user->rw_id;

        // --- Data untuk KPI Cards ---
        $reportCounts = $this->reportRepository->getStatusCounts($rwId);
        $totalReports = array_sum($reportCounts);
        $totalResidents = $this->residentRepository->countResidents($rwId);

        // --- Data untuk Bar Chart (Laporan per RW) ---
        $reportsByRw = $this->reportRepository->getReportCountsByRw();
        $rwLabels = $reportsByRw->pluck('rw_number')->map(fn($num) => "RW {$num}");
        $rwData = $reportsByRw->pluck('count');
        
        // --- Data untuk Line Chart (Laporan 7 hari terakhir) ---
        $dailyReports = $this->reportRepository->getDailyReportCounts($rwId);

        // --- Data untuk Doughnut Chart (Laporan per Kategori) ---
        $reportsByCategory = $this->reportRepository->getCategoryReportCounts($rwId);
        $categoryLabels = $reportsByCategory->pluck('name');
        $categoryData = $reportsByCategory->pluck('reports_count');
        
        // --- Data untuk Widget ---
        $latestReports = $this->reportRepository->getLatestReportsForAdmin($rwId, 5);
        $topReporters = $this->residentRepository->getTopReporters($rwId);

        return view('pages.admin.dashboard', [
            // KPI Data
            'totalReports' => $totalReports,
            'totalResidents' => $totalResidents,
            'inProcessCount' => $reportCounts[ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completedCount' => $reportCounts[ReportStatusEnum::COMPLETED->value] ?? 0,

            // Chart Data
            'dailyLabels' => $dailyReports['labels'],
            'dailyData' => $dailyReports['counts'],
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            'rwLabels' => $rwLabels, // Memastikan variabel ini dikirim
            'rwData' => $rwData,     // Memastikan variabel ini dikirim

            // Widget Data
            'latestReports' => $latestReports,
            'topReporters' => $topReporters,
        ]);
    }
}