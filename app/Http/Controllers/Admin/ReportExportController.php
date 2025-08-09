<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Exports\ReportsExport;
use App\Http\Controllers\Controller;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use Illuminate\Http\Request;
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

    /**
     * Menampilkan halaman form untuk filter ekspor.
     */
    public function create()
    {
        $residents = $this->residentRepository->getAllResidents();
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        $statuses = ReportStatusEnum::cases();

        return view('pages.admin.report.export', compact('residents', 'categories', 'statuses'));
    }

    /**
     * Memproses permintaan ekspor dengan filter.
     */
    public function store(Request $request)
    {
        $filters = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'resident_id' => 'nullable|exists:residents,id',
            'report_category_id' => 'nullable|exists:report_categories,id',
            'status' => 'nullable|string',
        ]);

        // 1. Cek terlebih dahulu apakah ada data yang cocok dengan filter
        $reportsToExport = $this->reportRepository->getFilteredReports($filters);

        // 2. Jika tidak ada data, kembalikan dengan pesan error
        if ($reportsToExport->isEmpty()) {
            Swal::error('Tidak Ada Data', 'Tidak ada data laporan yang cocok dengan filter yang Anda pilih.');
            return redirect()->back()->withInput();
        }

        // 3. Jika ada data, lanjutkan proses ekspor
        $fileName = 'laporan-custom-' . now()->format('d-m-Y-His') . '.xlsx';

        $export = new ReportsExport($filters);

        $response = Excel::download($export, $fileName);

        // Menambahkan header untuk mengatur cookie sebagai "sinyal" sukses
        $response->headers->set('Set-Cookie', cookie('export_success', 'true', 1));

        return $response;
    }
}