<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ReportController extends Controller
{
    private ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Report::with(['resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'latestStatus']);

        if ($user->hasRole('super-admin')) {
            $rwId = $request->query('rw');
            $rtId = $request->query('rt');
            if ($rtId) { $query->whereHas('resident', fn($q) => $q->where('rt_id', $rtId)); } 
            elseif ($rwId) { $query->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)); }
        } else {
            $query->whereHas('resident', fn($q) => $q->where('rw_id', $user->rw_id));
            $rtId = $request->query('rt');
            if ($rtId) { $query->whereHas('resident', fn($q) => $q->where('rt_id', $rtId)); }
        }

        $sortBy = $request->query('sort', 'latest_updated');
        switch ($sortBy) {
            case 'name_asc':
                $query->join('residents', 'reports.resident_id', '=', 'residents.id')
                      ->join('users', 'residents.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')->select('reports.*');
                break;
            case 'name_desc':
                $query->join('residents', 'reports.resident_id', '=', 'residents.id')
                      ->join('users', 'residents.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'desc')->select('reports.*');
                break;
            case 'oldest_created':
                $query->orderBy('reports.created_at', 'asc');
                break;
            case 'latest_created':
                $query->orderBy('reports.created_at', 'desc');
                break;
            case 'latest_updated':
            default:
                $query->leftJoin(DB::raw('(SELECT report_id, MAX(created_at) as last_status_date FROM report_statuses GROUP BY report_id) as latest_statuses'), 'reports.id', '=', 'latest_statuses.report_id')
                    ->orderBy(DB::raw('COALESCE(latest_statuses.last_status_date, reports.created_at)'), 'desc')
                    ->select('reports.*');
                break;
        }

        $reports = $query->paginate(10)->withQueryString();

        $rws = RW::all();
        $rts = RT::all();

        return view('pages.admin.report.index', compact('reports', 'rws', 'rts'));
    }

    public function show(string $id)
    {
        $report = $this->reportRepository->getReportById($id);
        return view('pages.admin.report.show', compact('report'));
    }

    public function update(Request $request, string $id)
    {
        $this->reportRepository->updateReport($request->all(), $id);
        Swal::success('Berhasil', 'Laporan berhasil diperbarui.');
        return redirect()->route('admin.report.index');
    }

    public function destroy(string $id)
    {
        $this->reportRepository->deleteReport($id);
        Swal::success('Berhasil', 'Laporan berhasil dihapus.');
        return redirect()->route('admin.report.index');
    }
}