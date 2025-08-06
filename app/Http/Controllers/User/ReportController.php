<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportRepositoryInterface $reportRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
    ){
        $this->reportRepository = $reportRepository;
    }

    public function show($code) 
    {
        $report = $this->reportRepository->getReportByCode($code);

        return view('pages.app.report.show', compact('report'));
    }
}
