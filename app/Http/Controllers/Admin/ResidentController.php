<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ResidentController extends Controller
{
    public function index()
    {
        $residents = Resident::with('user', 'rt', 'rw')->withCount('reports')->latest()->get();
        return view('pages.admin.resident.index', compact('residents'));
    }

    public function show(Resident $resident)
    {
        $resident->load('user', 'rt', 'rw');
        $reports = $resident->reports()->with('latestStatus')->get();

        $stats = [
            'total' => $reports->count(),
            'in_process' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === ReportStatusEnum::IN_PROCESS;
            })->count(),
            'completed' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === ReportStatusEnum::COMPLETED;
            })->count(),
            'rejected' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === ReportStatusEnum::REJECTED;
            })->count(),
        ];
        
        return view('pages.admin.resident.show', compact('resident', 'stats'));
    }

    public function destroy(Resident $resident)
    {
        DB::transaction(function () use ($resident) {
            $resident->reports()->delete();
            $resident->user()->delete();
            $resident->delete();
        });

        Swal::success('Berhasil', 'Data pelapor berhasil dihapus.');
        return redirect()->route('admin.resident.index');
    }
}