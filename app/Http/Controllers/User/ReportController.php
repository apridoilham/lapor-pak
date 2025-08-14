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
use App\Models\Rw;
use App\Services\ReportService;
use Illuminate\Database\Eloquent\Builder; // Pastikan ini ada
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportController extends Controller
{
    use AuthorizesRequests;

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

        return view('pages.app.report.index', compact('reports', 'categories', 'rws'));
    }

    /**
     * INI ADALAH METHOD YANG TELAH DIPERBAIKI
     * Method ini sekarang menghitung jumlah laporan untuk setiap status
     * dan mengirimkannya ke view.
     */
    public function myReport(Request $request)
    {
        $residentId = Auth::user()->resident->id;

        // 1. Tentukan status yang sedang aktif dari URL, default-nya 'delivered'
        $activeStatusValue = $request->query('status', ReportStatusEnum::DELIVERED->value);

        // 2. Ambil daftar laporan HANYA untuk status yang aktif (untuk ditampilkan di halaman)
        $reports = $this->reportRepository->getReportByResidentId($residentId, $activeStatusValue);

        // 3. Hitung jumlah total laporan untuk SETIAP status (untuk ditampilkan di kartu filter)
        // Pastikan Anda sudah menambahkan method countByStatus() di Repository Anda.
        $statusCounts = [
            'delivered' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::DELIVERED),
            'in_process' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::IN_PROCESS),
            'completed' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::COMPLETED),
            'rejected' => $this->reportRepository->countByStatus($residentId, ReportStatusEnum::REJECTED),
        ];

        // 4. Kirim kedua variabel ($reports dan $statusCounts) ke view
        return view('pages.app.report.my-report', compact('reports', 'statusCounts'));
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
        $this->reportRepository->updateReport($request->validated(), $report->id);
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