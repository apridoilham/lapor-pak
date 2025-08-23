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

        $reportCounts = $this->reportRepository->getStatusCounts($rwId);
        $totalReports = array_sum($reportCounts);
        $totalResidents = $this->residentRepository->countResidents($rwId);

        $reportsByRw = $this->reportRepository->getReportCountsByRw();
        $rwLabels = $reportsByRw->pluck('rw_number')->map(fn($num) => "RW {$num}");
        $rwData = $reportsByRw->pluck('count');
        
        $dailyReports = $this->reportRepository->getDailyReportCounts($rwId);

        $reportsByCategory = $this->reportRepository->getCategoryReportCounts($rwId);
        $categoryLabels = $reportsByCategory->pluck('name');
        $categoryData = $reportsByCategory->pluck('reports_count');
        
        $latestReports = $this->reportRepository->getLatestReportsForAdmin($rwId, 5);
        $topReporters = $this->residentRepository->getTopReporters($rwId);

        return view('pages.admin.dashboard', [
            'totalReports' => $totalReports,
            'totalResidents' => $totalResidents,
            'inProcessCount' => $reportCounts[ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completedCount' => $reportCounts[ReportStatusEnum::COMPLETED->value] ?? 0,

            'dailyLabels' => $dailyReports['labels'],
            'dailyData' => $dailyReports['counts'],
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            'rwLabels' => $rwLabels,
            'rwData' => $rwData,

            'latestReports' => $latestReports,
            'topReporters' => $topReporters,
        ]);
    }
}