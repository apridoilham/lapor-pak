<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportExportController extends Controller
{
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

    public function create()
    {
        $user = Auth::user();
        $residents = [];
        $rws = [];
        $rts = [];

        if ($user->hasRole('super-admin')) {
            $residents = $this->residentRepository->getAllResidents();
            $rws = Rw::orderBy('number')->get();
        } else {
            $residents = $this->residentRepository->getAllResidents($user->rw_id);
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }
        
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        $statuses = ReportStatusEnum::cases();

        return view('pages.admin.report.export', compact('residents', 'categories', 'statuses', 'rws', 'rts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $filters = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'resident_id' => 'nullable|exists:residents,id',
            'report_category_id' => 'nullable|exists:report_categories,id',
            'status' => 'nullable|string',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
        ]);

        if ($user->hasRole('admin')) {
            $filters['rw_id'] = $user->rw_id;
        }

        $reportsToExport = $this->reportRepository->getFilteredReports($filters);

        if ($reportsToExport->isEmpty()) {
            Swal::error('Tidak Ada Data', 'Tidak ada data laporan yang cocok dengan filter yang Anda pilih.');
            return redirect()->back()->withInput();
        }

        $fileName = 'laporan-custom-' . now()->format('d-m-Y-His') . '.xlsx';
        $export = new ReportsExport($filters);
        return Excel::download($export, $fileName);
    }
}