<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpsertReportStatusRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Traits\FileUploadTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportStatusController extends Controller
{
    use FileUploadTrait, AuthorizesRequests;

    private ReportRepositoryInterface $reportRepository;
    private ReportStatusRepositoryInterface $reportStatusRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportStatusRepositoryInterface $reportStatusRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportStatusRepository = $reportStatusRepository;
    }

    public function create($reportId)
    {
        $report = $this->reportRepository->getReportById($reportId);
        $this->authorize('manageStatus', $report);
        $statuses = ReportStatusEnum::cases();
        return view('pages.admin.report-status.create', compact('report', 'statuses'));
    }

    public function store(UpsertReportStatusRequest $request)
    {
        $report = $this->reportRepository->getReportById($request->report_id);
        $this->authorize('manageStatus', $report);

        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-status/image')) {
            $data['image'] = $path;
        }
        $data['created_by_role'] = 'admin';

        // Kirim Auth::id() ke repository
        $this->reportStatusRepository->createReportStatus($data, Auth::id());

        Swal::success('Success', 'Data Progress laporan berhasil ditambahkan!')->timerProgressBar();
        return redirect()->route('admin.report.show', $request->report_id);
    }

    public function edit(string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);
        $statuses = ReportStatusEnum::cases();
        return view('pages.admin.report-status.edit', compact('status', 'statuses'));
    }

    public function update(UpsertReportStatusRequest $request, string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);

        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-status/image', $status->image)) {
            $data['image'] = $path;
        }

        // Kirim Auth::id() ke repository
        $this->reportStatusRepository->updateReportStatus($data, $id, Auth::id());

        Swal::success('Success', 'Data progress laporan berhasil diubah!')->timerProgressBar();
        return redirect()->route('admin.report.show', $request->report_id);
    }

    public function destroy(string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);

        if ($status->image) {
            Storage::disk('public')->delete($status->image);
        }

        $this->reportStatusRepository->deleteReportStatus($id);

        Swal::success('Success', 'Data progress laporan berhasil dihapus!')->timerProgressBar();
        return redirect()->route('admin.report.show', $status->report_id);
    }
}