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

        $dailyReports = $this->reportRepository->getDailyReportCounts($rwId);

        $reportsByCategory = $this->reportRepository->getCategoryReportCounts($rwId);
        $categoryLabels = $reportsByCategory->pluck('name');
        $categoryData = $reportsByCategory->pluck('reports_count');

        $areaLabels = collect();
        $areaData = collect();
        if ($user->hasRole('super-admin')) {
            $reportsByArea = $this->reportRepository->getReportCountsByRw();
            $areaLabels = $reportsByArea->pluck('rw_number')->map(fn($num) => "RW {$num}");
            $areaData = $reportsByArea->pluck('count');
        } else {
            $reportsByArea = $this->reportRepository->getReportCountsByRt($rwId);
            $areaLabels = $reportsByArea->pluck('rt_number')->map(fn($num) => "RT {$num}");
            $areaData = $reportsByArea->pluck('count');
        }

        $topReporters = $this->residentRepository->getTopReporters($rwId);
        $latestReports = $this->reportRepository->getLatestReportsForAdmin($rwId, 5);

        return view('pages.admin.dashboard', [
            'totalReports' => $totalReports,
            'totalResidents' => $totalResidents,
            'inProcessCount' => $reportCounts[ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completedCount' => $reportCounts[ReportStatusEnum::COMPLETED->value] ?? 0,
            'dailyLabels' => $dailyReports['labels'],
            'dailyData' => $dailyReports['counts'],
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
            'areaLabels' => $areaLabels,
            'areaData' => $areaData,
            'topReporters' => $topReporters,
            'latestReports' => $latestReports,
        ]);
    }
}