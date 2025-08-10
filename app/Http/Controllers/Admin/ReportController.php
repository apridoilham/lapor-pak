<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Models\Rt;
use App\Models\Rw;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportController extends Controller
{
    use FileUploadTrait;

    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;
    private ResidentRepositoryInterface $residentRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository,
        ResidentRepositoryInterface $residentRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
        $this->residentRepository = $residentRepository;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $rws = [];
        $rts = [];
        $reports = [];

        $filterRwId = $request->input('rw');
        $filterRtId = $request->input('rt');

        if ($user->hasRole('super-admin')) {
            $reports = $this->reportRepository->getAllReportsForAdmin($request, $filterRwId, $filterRtId);
            $rws = Rw::orderBy('number')->get();
        } else {
            $reports = $this->reportRepository->getAllReportsForAdmin($request, $user->rw_id, $filterRtId);
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }

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
        
        if ($path = $this->handleFileUpload($request, 'image', 'assets/report/image')) {
            $data['image'] = $path;
        }

        $this->reportRepository->updateReport($data, $id);

        Swal::success('Success', 'Data laporan berhasil diupdate!')->timerProgressBar();

        return redirect()->route('admin.report.index');
    }

    public function destroy(string $id)
    {
        $this->reportRepository->deleteReport($id);

        Swal::success('Success', 'Data laporan berhasil dihapus!')->timerProgressBar();

        return redirect()->route('admin.report.index');
    }
}