<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $rws = Rw::orderBy('number')->get();
        $rts = collect();
        if ($request->filled('rw_id')) {
            $rts = Rt::where('rw_id', $request->rw_id)->orderBy('number')->get();
        }

        $query = Resident::complete()->with(['user', 'rt', 'rw'])->withCount('reports');

        if ($request->filled('rw_id')) {
            $query->where('rw_id', $request->rw_id);
        }
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }
        
        $sortBy = $request->input('sort', 'terbaru');
        switch ($sortBy) {
            case 'terlama':
                $query->oldest('created_at');
                break;
            case 'nama_asc':
                $query->join('users', 'residents.user_id', '=', 'users.id')
                        ->orderBy('users.name', 'asc')
                        ->select('residents.*');
                break;
            case 'nama_desc':
                $query->join('users', 'residents.user_id', '=', 'users.id')
                        ->orderBy('users.name', 'desc')
                        ->select('residents.*');
                break;
            case 'laporan_terbanyak':
                $query->orderBy('reports_count', 'desc');
                break;
            case 'laporan_sedikit':
                $query->orderBy('reports_count', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest('created_at');
                break;
        }

        $perPage = $request->input('per_page', 10);

        $residents = $query->paginate($perPage)->withQueryString();

        return view('pages.admin.resident.index', compact('residents', 'rws', 'rts', 'perPage'));
    }

    public function show(Resident $resident)
    {
        $resident->load('user', 'rt', 'rw');
        $reports = $resident->reports()->with('latestStatus')->get();

        $stats = [
            'total' => $reports->count(),
            'in_process' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === \App\Enums\ReportStatusEnum::IN_PROCESS;
            })->count(),
            'completed' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === \App\Enums\ReportStatusEnum::COMPLETED;
            })->count(),
            'rejected' => $reports->filter(function ($report) {
                return optional($report->latestStatus)->status === \App\Enums\ReportStatusEnum::REJECTED;
            })->count(),
        ];
        
        return view('pages.admin.resident.show', compact('resident', 'stats'));
    }
}