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
            $query->where('residents.rw_id', $request->rw_id);
        }
        if ($request->filled('rt_id')) {
            $query->where('residents.rt_id', $request->rt_id);
        }
        
        $sortBy = $request->input('sort', 'terbaru');
        switch ($sortBy) {
            case 'terlama':
                $query->oldest('created_at');
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
        $resident->load('user', 'rt', 'rw', 'reports.latestStatus');

        $reportCounts = $resident->reports->groupBy('latestStatus.status.value')->map->count();

        $stats = [
            'total' => $resident->reports->count(),
            'delivered' => $reportCounts[\App\Enums\ReportStatusEnum::DELIVERED->value] ?? 0,
            'in_process' => $reportCounts[\App\Enums\ReportStatusEnum::IN_PROCESS->value] ?? 0,
            'completed' => $reportCounts[\App\Enums\ReportStatusEnum::COMPLETED->value] ?? 0,
            'rejected' => $reportCounts[\App\Enums\ReportStatusEnum::REJECTED->value] ?? 0,
        ];
        
        return view('pages.admin.resident.show', compact('resident', 'stats'));
    }
}