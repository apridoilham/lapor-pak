<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
// Hapus 'use Carbon\Carbon;' karena tidak lagi digunakan di sini
use Illuminate\Support\Facades\Auth;
// Hapus 'use Illuminate\Support\Str;' karena tidak lagi digunakan di sini

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
        $rwLabels = $reportsByRw->pluck('rw_number');
        $rwData = $reportsByRw->pluck('count');

        // --- Data untuk Line Chart (Laporan 7 hari terakhir) ---
        // Logika ini sekarang lebih sederhana!
        $dailyReports = $this->reportRepository->getDailyReportCounts($rwId);
        $dailyLabels = $dailyReports['labels'];
        $dailyData = $dailyReports['counts'];

        return view('pages.admin.dashboard', [
            // KPI Data
            'totalReports' => $totalReports,
            'totalResidents' => $totalResidents,
            'deliveredCount' => $reportCounts[ReportStatusEnum::DELIVERED->value] ?? 0,
            'inProcessCount' => $reportCounts[ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completedCount' => $reportCounts[ReportStatusEnum::COMPLETED->value] ?? 0,
            'rejectedCount' => $reportCounts[ReportStatusEnum::REJECTED->value] ?? 0,

            // Chart Data
            'rwLabels' => $rwLabels,
            'rwData' => $rwData,
            'dailyLabels' => $dailyLabels,
            'dailyData' => $dailyData,
        ]);
    }
}