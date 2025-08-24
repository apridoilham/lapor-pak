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
use Illuminate\Support\Facades\Validator; // Import Validator
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'resident_id' => 'nullable|exists:residents,id',
            'report_category_id' => 'nullable|exists:report_categories,id',
            'status' => 'nullable|string',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $filters = $validator->validated();
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $filters['rw_id'] = $user->rw_id;
        }

        $reportsToExport = $this->reportRepository->getFilteredReports($filters);

        if ($reportsToExport->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data laporan yang cocok dengan filter yang Anda pilih.'
            ], 422);
        }

        $startDate = Carbon::parse($filters['start_date'])->format('d-m-Y');
        $endDate = !empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->format('d-m-Y') : null;

        if ($endDate && $startDate !== $endDate) {
            $dateRange = "{$startDate}_sampai_{$endDate}";
        } else {
            $dateRange = $startDate;
        }

        $fileName = "laporan-bsb-{$dateRange}.xlsx";
        
        $export = new ReportsExport($filters);
        return Excel::download($export, $fileName);
    }
}