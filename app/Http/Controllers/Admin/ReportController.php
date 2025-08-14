<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportVisibilityEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Models\RW;
use App\Models\RT;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // TAMBAHKAN INI
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportController extends Controller
{
    use FileUploadTrait;

    private ReportRepositoryInterface $reportRepository;
    private ResidentRepositoryInterface $residentRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ResidentRepositoryInterface $residentRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->residentRepository = $residentRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $rwId = $user->hasRole('super-admin') ? $request->query('rw') : $user->rw_id;
        $rtId = $request->query('rt');

        $reports = $this->reportRepository->getAllReportsForAdmin($request, $rwId, $rtId);
        $rws = RW::all();
        $rts = RT::all();

        return view('pages.admin.report.index', compact('reports', 'rws', 'rts'));
    }

    public function create()
    {
        $residents = $this->residentRepository->getAllResidents();
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        return view('pages.admin.report.create', compact('residents', 'categories'));
    }

    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();

        $data['code'] = config('report.code_prefix.admin') . mt_rand(100000, 999999);
        $data['visibility'] = ReportVisibilityEnum::PUBLIC->value;

        if ($path = $this->handleFileUpload($request, 'image', 'assets/report/image')) {
            $data['image'] = $path;
        }

        $this->reportRepository->createReport($data);

        Swal::success('Success', 'Data laporan berhasil ditambahkan!')->timerProgressBar();
        return redirect()->route('admin.report.index');
    }

    public function show(string $id)
    {
        $report = $this->reportRepository->getReportById($id);
        return view('pages.admin.report.show', compact('report'));
    }

    public function edit(string $id)
    {
        $report = $this->reportRepository->getReportById($id);
        $residents = $this->residentRepository->getAllResidents();
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        return view('pages.admin.report.edit', compact('report', 'residents', 'categories'));
    }

    public function update(UpdateReportRequest $request, string $id)
    {
        $data = $request->validated();
        $oldImage = $this->reportRepository->getReportById($id)->image;

        if ($path = $this->handleFileUpload($request, 'image', 'assets/report/image', $oldImage)) {
            $data['image'] = $path;
        }

        $this->reportRepository->updateReport($data, $id);

        Swal::success('Success', 'Data laporan berhasil diperbarui!')->timerProgressBar();
        return redirect()->route('admin.report.index');
    }

    public function destroy(string $id)
    {
        $report = $this->reportRepository->getReportById($id);

        if ($report->image) {
            Storage::disk('public')->delete($report->image);
        }

        $this->reportRepository->deleteReport($id);

        Swal::success('Success', 'Data laporan berhasil dihapus!')->timerProgressBar();
        return redirect()->route('admin.report.index');
    }
}