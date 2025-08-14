<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
    }

    public function index(Request $request)
    {
        $categories = $this->reportCategoryRepository->getAllReportCategories();
        $reports = $this->reportRepository->getLatestReportsForUser($request);

        // --- TAMBAHAN DATA UNTUK FILTER ---
        $rws = Rw::orderBy('number')->get();

        $rtId = $request->input('rt');
        $selectedRt = $rtId ? Rt::find($rtId) : null;
        // --- AKHIR TAMBAHAN ---

        return view('pages.app.home', compact('categories', 'reports', 'rws', 'selectedRt'));
    }
}