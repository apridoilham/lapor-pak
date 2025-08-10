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
    ){
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
    }

    public function index(Request $request)
    {
        $rwId = $request->input('rw');
        $rtId = $request->input('rt');

        $categories = $this->reportCategoryRepository->getAllReportCategories();
        $reports = $this->reportRepository->getLatesReports($rwId, $rtId);
        $rws = Rw::orderBy('number')->get();
        $selectedRt = $rtId ? Rt::find($rtId) : null;

        return view('pages.app.home', compact('categories', 'reports', 'rws', 'selectedRt'));
    }
}