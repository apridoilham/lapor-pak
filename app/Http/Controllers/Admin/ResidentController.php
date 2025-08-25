<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\ReportStatusEnum;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rws = $user->hasRole('super-admin') ? Rw::orderBy('number')->get() : collect();
        $rts = collect();

        $query = Resident::with(['user', 'rt', 'rw'])->withCount('reports');

        if ($user->hasRole('admin')) {
            $query->where('rw_id', $user->rw_id);
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }

        if ($request->filled('rw_id') && $user->hasRole('super-admin')) {
            $query->where('rw_id', $request->rw_id);
            $rts = Rt::where('rw_id', $request->rw_id)->orderBy('number')->get();
        }

        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
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
        $resident->load('user', 'rt', 'rw', 'reports.latestStatus', 'reports.reportCategory');

        $reportCounts = DB::table('reports')
            ->select(DB::raw('COALESCE(latest_statuses.status, "delivered") as final_status'), DB::raw('count(*) as count'))
            ->leftJoin(DB::raw('(SELECT report_id, status FROM report_statuses WHERE id IN (SELECT MAX(id) FROM report_statuses GROUP BY report_id)) as latest_statuses'), 'reports.id', '=', 'latest_statuses.report_id')
            ->where('reports.resident_id', $resident->id)
            ->groupBy('final_status')
            ->pluck('count', 'final_status');
            
        $stats = [
            'total' => $resident->reports->count(),
            'delivered' => $reportCounts['delivered'] ?? 0,
            'in_process' => $reportCounts['in_process'] ?? 0,
            'completed' => $reportCounts['completed'] ?? 0,
            'rejected' => $reportCounts['rejected'] ?? 0,
        ];
        
        return view('pages.admin.resident.show', compact('resident', 'stats'));
    }
}