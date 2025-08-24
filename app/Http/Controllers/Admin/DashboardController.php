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
        $isSuperAdmin = $user->hasRole('super-admin');
        $rwId = $isSuperAdmin ? null : $user->rw_id;

        $reportCounts = $this->reportRepository->getStatusCounts($rwId);
        $totalReports = array_sum($reportCounts);
        $totalResidents = $this->residentRepository->countResidents($rwId);

        $distributionChartTitle = 'Distribusi Laporan per RW';
        if ($isSuperAdmin) {
            $reportsByLocation = $this->reportRepository->getReportCountsByRw();
            $locationLabels = $reportsByLocation->pluck('rw_number')->map(fn($num) => "RW {$num}");
            $locationData = $reportsByLocation->pluck('count');
        } else {
            $distributionChartTitle = "Distribusi Laporan per RT di RW {$user->rw->number}";
            $reportsByLocation = $this->reportRepository->getReportCountsByRt($rwId);
            $locationLabels = $reportsByLocation->pluck('rt_number')->map(fn($num) => "RT {$num}");
            $locationData = $reportsByLocation->pluck('count');
        }
        
        $dailyReports = $this->reportRepository->getDailyReportCounts($rwId);
        $reportsByCategory = $this->reportRepository->getCategoryReportCounts($rwId);
        $latestReports = $this->reportRepository->getLatestReportsForAdmin($rwId, 5);
        $topReporters = $this->residentRepository->getTopReporters($rwId);

        return view('pages.admin.dashboard', [
            'totalReports' => $totalReports,
            'totalResidents' => $totalResidents,
            'deliveredCount' => $reportCounts[ReportStatusEnum::DELIVERED->value] ?? 0,
            'inProcessCount' => $reportCounts[ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completedCount' => $reportCounts[ReportStatusEnum::COMPLETED->value] ?? 0,
            'rejectedCount' => $reportCounts[ReportStatusEnum::REJECTED->value] ?? 0,
            'dailyLabels' => $dailyReports['labels'],
            'dailyData' => $dailyReports['counts'],
            'categoryLabels' => $reportsByCategory->pluck('name'),
            'categoryData' => $reportsByCategory->pluck('reports_count'),
            'distributionChartTitle' => $distributionChartTitle,
            'locationLabels' => $locationLabels,
            'locationData' => $locationData,
            'latestReports' => $latestReports,
            'topReporters' => $topReporters,
        ]);
    }
}