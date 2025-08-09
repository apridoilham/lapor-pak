<?php

namespace App\Http\Controllers\User;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;
    private ReportService $reportService;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository,
        ReportService $reportService
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        if ($request->category) {
            $reports = $this->reportRepository->getReportsByCategory($request->category);
        } else {
            $reports = $this->reportRepository->getAllReports($request);
        }

        // Ambil semua kategori untuk modal filter
        $categories = $this->reportCategoryRepository->getAllReportCategories();

        return view('pages.app.report.index', compact('reports', 'categories'));
    }

    public function myReport(Request $request)
    {
        $residentId = Auth::user()->resident->id;
        $statusValue = $request->query('status', ReportStatusEnum::DELIVERED->value);
        $statusEnum = ReportStatusEnum::tryFrom($statusValue) ?? ReportStatusEnum::DELIVERED;
        $reports = $this->reportRepository->getReportByResidentId($residentId, $statusEnum->value);
        return view('pages.app.report.my-report', compact('reports'));
    }

    public function show($code)
    {
        $report = $this->reportRepository->getReportByCode($code);
        return view('pages.app.report.show', compact('report'));
    }

    public function take()
    {
        return view('pages.app.report.take');
    }

    public function preview()
    {
        return view('pages.app.report.preview');
    }

    public function create()
    {
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        return view('pages.app.report.create', compact('categories'));
    }

    public function store(StoreReportRequest $request)
    {
        $this->reportService->createReportForUser(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('report.success');
    }

    public function success()
    {
        return view('pages.app.report.success');
    }
}