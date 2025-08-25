<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    use AuthorizesRequests;

    private ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function index(Request $request)
    {
        $request->validate([
            'sort' => ['nullable', Rule::in(['latest_updated', 'latest_created', 'oldest_created', 'name_asc', 'name_desc'])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();
        $sortBy = $request->query('sort', 'latest_updated');
        $searchQuery = $request->query('search');

        $query = Report::with(['resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'latestStatus']);

        if ($user->hasRole('admin')) {
            $query->whereHas('resident', function ($q) use ($user) {
                $q->where('rw_id', $user->rw_id);
            });
        }

        $query->when($searchQuery, function ($q, $search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('resident.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        });

        switch ($sortBy) {
            case 'name_asc':
                $query->select('reports.*')->join('residents', 'reports.resident_id', '=', 'residents.id')
                    ->join('users', 'residents.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc');
                break;
            case 'name_desc':
                $query->select('reports.*')->join('residents', 'reports.resident_id', '=', 'residents.id')
                    ->join('users', 'residents.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc');
                break;
            case 'oldest_created':
                $query->orderBy('reports.created_at', 'asc');
                break;
            case 'latest_created':
                $query->orderBy('reports.created_at', 'desc');
                break;
            case 'latest_updated':
            default:
                $query->select('reports.*')
                    ->addSelect(['last_status_date' => DB::table('report_statuses')
                        ->select('created_at')
                        ->whereColumn('report_id', 'reports.id')
                        ->orderByDesc('created_at')
                        ->limit(1)
                    ])
                    ->orderBy(DB::raw('COALESCE(last_status_date, reports.created_at)'), 'desc');
                break;
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('pages.admin.report.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $this->authorize('manageStatus', $report);
        $report->load('resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'reportStatuses', 'comments.user.resident');
        return view('pages.admin.report.show', compact('report'));
    }

    public function destroy(Report $report)
    {
        $this->authorize('manageStatus', $report);
        $this->reportRepository->deleteReport($report->id);
        
        return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dihapus.');
    }
}