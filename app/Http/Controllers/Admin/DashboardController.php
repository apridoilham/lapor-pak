<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\ReportCategory;
use App\Models\Rw;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $viewData = [];

        if ($user->hasRole('super-admin')) {
            $viewData = $this->getSuperAdminDashboardData();
        } else {
            $viewData = $this->getAdminDashboardData($user->rw_id);
        }

        return view('pages.admin.dashboard', $viewData);
    }

    private function getSuperAdminDashboardData(): array
    {
        $data = $this->getCommonDashboardData();
        $reportsByRw = $this->reportRepository->getReportCountsByRw();
        $data['rwLabels'] = $reportsByRw->pluck('rw_number')->map(fn($num) => "RW {$num}")->toArray();
        $data['rwData'] = $reportsByRw->pluck('count')->toArray();
        $data['rwNumber'] = null;
        
        return $data;
    }

    private function getAdminDashboardData(int $rwId): array
    {
        $data = $this->getCommonDashboardData($rwId);
        $rw = Rw::find($rwId);
        $data['rwNumber'] = $rw ? $rw->number : '';

        $reportsByRt = $this->reportRepository->getReportCountsByRt($rwId);
        $data['rtLabels'] = $reportsByRt->pluck('rt_number')->map(fn($num) => "RT {$num}")->toArray();
        $data['rtData'] = $reportsByRt->pluck('count')->toArray();

        return $data;
    }

    private function getCommonDashboardData(int $rwId = null): array
    {
        $reportCategoryCount = ReportCategory::count();
        $reportCount = $this->reportRepository->countReports($rwId);
        $residentCount = $this->residentRepository->countResidents($rwId);
        $latestReports = $this->reportRepository->getLatestReportsForAdmin($rwId);
        
        $statusCounts = $this->reportRepository->getStatusCounts($rwId);

        $categoriesWithCount = $this->reportRepository->getCategoryReportCounts($rwId);
        $categoryLabels = $categoriesWithCount->pluck('name')->toArray();
        $categoryData = $categoriesWithCount->pluck('reports_count')->toArray();

        $reportDaily = $this->reportRepository->getDailyReportCounts($rwId);
        $dailyLabels = [];
        $dailyData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            
            $dailyLabels[] = $date->isoFormat('D MMM');
            $dailyData[] = $reportDaily[$dateString] ?? 0;
        }

        $data = compact(
            'reportCategoryCount',
            'reportCount',
            'residentCount',
            'latestReports',
            'categoryLabels',
            'categoryData',
            'dailyLabels',
            'dailyData'
        );
        
        $data['deliveredCount'] = $statusCounts[ReportStatusEnum::DELIVERED->value];
        $data['inProcessCount'] = $statusCounts[ReportStatusEnum::IN_PROCESS->value];
        $data['completedCount'] = $statusCounts[ReportStatusEnum::COMPLETED->value];
        $data['rejectedCount'] = $statusCounts[ReportStatusEnum::REJECTED->value];

        return $data;
    }
}