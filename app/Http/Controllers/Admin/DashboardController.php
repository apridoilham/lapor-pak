<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\Resident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function index()
    {
        // Data untuk kartu statistik
        $reportCategoryCount = ReportCategory::count();
        $reportCount = Report::count();
        $residentCount = Resident::count();
        
        // Data untuk tabel laporan terbaru
        $latestReports = $this->reportRepository->getLatesReports();

        // ▼▼▼ KODE BARU UNTUK MENYIAPKAN DATA GRAFIK ▼▼▼
        // Ambil semua kategori beserta jumlah laporannya menggunakan withCount()
        $categoriesWithCount = ReportCategory::withCount('reports')->get();

        // Pisahkan data menjadi label (nama kategori) dan data (jumlah laporan)
        $categoryLabels = $categoriesWithCount->pluck('name')->toArray();
        $categoryData = $categoriesWithCount->pluck('reports_count')->toArray();
        // ▲▲▲ AKHIR DARI KODE BARU ▲▲▲

        return view('pages.admin.dashboard', compact(
            'reportCategoryCount',
            'reportCount',
            'residentCount',
            'latestReports',
            'categoryLabels', // <-- Kirim data label ke view
            'categoryData'    // <-- Kirim data jumlah ke view
        ));
    }
}