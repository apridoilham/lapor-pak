<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository
    ) {
    }

    public function index()
    {
        $stats = $this->reportRepository->countStatusesByResidentId(Auth::user()->resident->id);

        return view('pages.app.profile', [
            'activeReportsCount' => $stats['active'],
            'completedReportsCount' => $stats['completed'],
            'rejectedReportsCount' => $stats['rejected'],
        ]);
    }
}