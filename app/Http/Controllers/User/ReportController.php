<?php

namespace App\Http\Controllers\User;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteReportRequest;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateUserReportRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Models\Report;
use App\Models\Rt;
use App\Models\Rw;
use App\Services\ReportService;
use App\Traits\FileUploadTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportController extends Controller
{
    use AuthorizesRequests, FileUploadTrait;

    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;
    private ReportStatusRepositoryInterface $reportStatusRepository;
    private ReportService $reportService;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository,
        ReportStatusRepositoryInterface $reportStatusRepository,
        ReportService $reportService
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
        $this->reportStatusRepository = $reportStatusRepository;
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $reports = $this->reportRepository->getAllReportsForUser($request);
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        $rws = Rw::orderBy('number')->get();

        $rts = collect();
        if ($request->filled('rw')) {
            $rts = Rt::where('rw_id', $request->rw)->orderBy('number')->get();
        }

        return view('pages.app.report.index', compact('reports', 'categories', 'rws', 'rts'));
    }

    public function myReport(Request $request)
    {
        $residentId = Auth::user()->resident->id;
        $activeStatusValue = $request->query('status', ReportStatusEnum::DELIVERED->value);
        $reports = $this->reportRepository->getReportByResidentId($residentId, $activeStatusValue);
        $statusCounts = [
            'delivered' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::DELIVERED),
            'in_process' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::IN_PROCESS),
            'completed' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::COMPLETED),
            'rejected' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::REJECTED),
        ];
        return view('pages.app.report.my-report', compact('reports', 'statusCounts'));
    }

    public function show($code)
    {
        $report = $this->reportRepository->getReportByCode($code);
        $isReportOwner = auth()->check() && auth()->id() === $report->resident->user_id;
        return view('pages.app.report.show', compact('report', 'isReportOwner'));
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
        $report = $this->reportService->createReportForUser(
            $request->validated(),
            $request->user(),
            $request->file('image')
        );

        return redirect()->route('report.summary', ['report' => $report->code]);
    }

    public function summary(Report $report)
    {
        return view('pages.app.report.summary', compact('report'));
    }

    public function success()
    {
        return view('pages.app.report.success');
    }

    public function edit(Report $report)
    {
        $this->authorize('update', $report);
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        return view('pages.app.report.edit', compact('report', 'categories'));
    }

    public function update(UpdateUserReportRequest $request, Report $report)
    {
        $this->authorize('update', $report);

        $validatedData = $request->validated();

        if ($path = $this->handleFileUpload($request, 'image', 'assets/report/image', $report->image)) {
            $validatedData['image'] = $path;
        }

        $this->reportRepository->updateReport($validatedData, $report->id);
        Swal::success('Berhasil', 'Laporan Anda telah berhasil diperbarui.');
        return redirect()->route('report.myreport');
    }

    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);
        $this->reportRepository->deleteReport($report->id);
        Swal::success('Berhasil', 'Laporan Anda telah berhasil dihapus.');
        return redirect()->route('report.myreport');
    }

    public function showCompleteForm(Report $report)
    {
        $this->authorize('complete', $report);
        return view('pages.app.report.complete', compact('report'));
    }

    public function complete(CompleteReportRequest $request, Report $report)
    {
        $this->authorize('complete', $report);

        $this->reportStatusRepository->createReportStatus([
            'report_id' => $report->id,
            'status' => ReportStatusEnum::COMPLETED,
            'description' => $request->description,
            'created_by_role' => 'resident',
        ]);

        Swal::success('Berhasil', 'Laporan Anda telah ditandai sebagai selesai.');
        return redirect()->route('report.myreport', ['status' => 'completed']);
    }
}