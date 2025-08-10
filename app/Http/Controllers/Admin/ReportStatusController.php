<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportStatusRequest;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateReportStatusRequest;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportStatusController extends Controller
{
    use FileUploadTrait;

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
        return view('pages.admin.report-status.create', compact('report'));
    }

    public function store(StoreReportStatusRequest $request)
    {
        $report = $this->reportRepository->getReportById($request->report_id);
        $this->authorize('manageStatus', $report);

        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-status/image')) {
            $data['image'] = $path;
        }
        $data['created_by_role'] = 'admin';

        $this->reportStatusRepository->createReportStatus($data);

        Swal::success('Success', 'Data Progress laporan berhasil ditambahkan!')->timerProgressBar();
        return redirect()->route('admin.report.show', $request->report_id);
    }

    public function edit(string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);
        return view('pages.admin.report-status.edit', compact('status'));
    }

    public function update(UpdateReportStatusRequest $request, string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);

        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report-status/image')) {
            $data['image'] = $path;
        }

        $this->reportStatusRepository->updateReportStatus($data, $id);

        Swal::success('Success', 'Data progress laporan berhasil diubah!')->timerProgressBar();
        return redirect()->route('admin.report.show', $request->report_id);
    }

    public function destroy(string $id)
    {
        $status = $this->reportStatusRepository->getReportStatusById($id);
        $this->authorize('manageStatus', $status->report);

        $this->reportStatusRepository->deleteReportStatus($id);

        Swal::success('Success', 'Data progress laporan berhasil dihapus!')->timerProgressBar();
        return redirect()->route('admin.report.show', $status->report_id);
    }
}