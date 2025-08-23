<?php

namespace App\Repositories;

use App\Enums\ReportStatusEnum;
use App\Enums\ReportVisibilityEnum;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ReportRepository implements ReportRepositoryInterface
{
    private function applyVisibilityFilter(Builder $query): Builder
    {
        $user = Auth::user();

        return $query->where(function ($q) use ($user) {
            $q->where('visibility', ReportVisibilityEnum::PUBLIC);

            if ($user && $user->resident) {
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('visibility', ReportVisibilityEnum::RW)
                        ->whereHas('resident', function ($q3) use ($user) {
                            $q3->where('rw_id', $user->resident->rw_id);
                        });
                });
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('visibility', ReportVisibilityEnum::RT)
                        ->whereHas('resident', function ($q3) use ($user) {
                            $q3->where('rt_id', $user->resident->rt->id);
                        });
                });
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('visibility', ReportVisibilityEnum::PRIVATE)
                        ->where('resident_id', $user->resident->id);
                });
            }
        });
    }

    public function getAllReportsForUser(Request $request): LengthAwarePaginator
    {
        $query = Report::with('resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'latestStatus');

        $this->applyVisibilityFilter($query);

        if ($categoryName = $request->input('category')) {
            $query->whereHas('reportCategory', function (Builder $q) use ($categoryName) {
                $q->where('name', 'like', '%' . $categoryName . '%');
            });
        }
        
        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $rwId = $request->input('rw');
        $rtId = $request->input('rt');

        if ($rwId || $rtId) {
            $query->whereHas('resident', function ($q) use ($rwId, $rtId) {
                if ($rtId) {
                    $q->where('rt_id', $rtId);
                } elseif ($rwId) {
                    $q->where('rw_id', $rwId);
                }
            });
        }

        if ($request->input('sort') === 'terlama') {
            $query->oldest('created_at');
        } else {
            $query->latest('created_at');
        }
        
        return $query->paginate(10)->withQueryString();
    }
    
    public function getLatestReportsForUser(Request $request): LengthAwarePaginator
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus');
        $this->applyVisibilityFilter($query);
        
        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest('updated_at')->paginate(10)->withQueryString();
    }

    public function getAllReportsForAdmin(Request $request, int $rwId = null, int $rtId = null): EloquentCollection
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus')->whereHas('resident');
        if ($rtId) { $query->whereHas('resident', fn($q) => $q->where('rt_id', $rtId)); } 
        elseif ($rwId) { $query->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)); }
        return $query->latest()->get();
    }

    public function getLatestReportsForAdmin(?int $rwId = null, int $limit = 5): EloquentCollection
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus')->whereHas('resident');
        if ($rwId) { $query->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)); }
        return $query->latest('created_at')->take($limit)->get();
    }

    public function getReportByResidentId(int $residentId, ?string $status): EloquentCollection
    {
        $query = Report::where('resident_id', $residentId)->with('latestStatus');
        if ($status) { $query->whereHas('latestStatus', fn(Builder $q) => $q->where('status', $status)); }
        return $query->latest()->get();
    }

    public function getReportById(int $id) { return Report::with('resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'reportStatuses')->findOrFail($id); }
    public function getReportByCode(string $code) { return Report::with('resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'reportStatuses')->where('code', $code)->firstOrFail(); }

    public function createReport(array $data)
    {
        $report = Report::create($data);
        $report->reportStatuses()->create(['status' => ReportStatusEnum::DELIVERED, 'description' => 'Laporan berhasil diterima oleh sistem dan akan segera diproses.', 'created_by_role' => 'resident']);
        return $report;
    }

    public function updateReport(array $data, int $id) { return Report::findOrFail($id)->update($data); }
    public function deleteReport(int $id) { return Report::findOrFail($id)->delete(); }

    public function countStatusesByResidentId(int $residentId): array
    {
        $active = Report::where('resident_id', $residentId)->whereHas('latestStatus', fn(Builder $q) => $q->whereIn('status', [ReportStatusEnum::DELIVERED, ReportStatusEnum::IN_PROCESS]))->count();
        $completed = Report::where('resident_id', $residentId)->whereHas('latestStatus', fn(Builder $q) => $q->where('status', ReportStatusEnum::COMPLETED))->count();
        $rejected = Report::where('resident_id', $residentId)->whereHas('latestStatus', fn(Builder $q) => $q->where('status', ReportStatusEnum::REJECTED))->count();
        return ['active' => $active, 'completed' => $completed, 'rejected' => $rejected];
    }
    
    public function countByStatus(int $residentId, ReportStatusEnum $status): int
    {
        return Report::where('resident_id', $residentId)->whereHas('latestStatus', fn(Builder $q) => $q->where('status', $status->value))->count();
    }

    public function getFilteredReports(array $filters): EloquentCollection
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus');
        if (!empty($filters['start_date'])) { $query->whereDate('created_at', '>=', $filters['start_date']); }
        if (!empty($filters['end_date'])) { $query->whereDate('created_at', '<=', $filters['end_date']); }
        if (!empty($filters['resident_id'])) { $query->where('resident_id', $filters['resident_id']); }
        if (!empty($filters['report_category_id'])) { $query->where('report_category_id', $filters['report_category_id']); }
        if (!empty($filters['status'])) { $query->whereHas('latestStatus', fn($q) => $q->where('status', $filters['status'])); }
        if (!empty($filters['rw_id'])) { $query->whereHas('resident', fn($q) => $q->where('rw_id', $filters['rw_id'])); }
        if (!empty($filters['rt_id'])) { $query->whereHas('resident', fn($q) => $q->where('rt_id', $filters['rt_id'])); }
        return $query->get();
    }

    public function countReports(int $rwId = null): int { return Report::whereHas('resident')->when($rwId, fn($q) => $q->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)))->count(); }

    public function getCategoryReportCounts(int $rwId = null): EloquentCollection
    {
        return ReportCategory::withCount(['reports' => function ($query) use ($rwId) {
            $query->whereHas('resident');
            if ($rwId) { $query->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)); }
        }])->having('reports_count', '>', 0)->get();
    }

    public function getDailyReportCounts(int $rwId = null): Collection
    {
        $query = Report::query()->whereHas('resident')->when($rwId, fn($q) => $q->whereHas('resident', fn($q) => $q->where('rw_id', $rwId)));
        $now = Carbon::now('Asia/Jakarta');
        $reports = $query->whereBetween('created_at', [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')->orderBy('date', 'ASC')->get();
        $labels = collect(); $counts = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $labels->push($date->isoFormat('dddd, D MMMM'));
            $found = $reports->firstWhere('date', $date->format('Y-m-d'));
            $counts->push($found ? $found->count : 0);
        }
        return collect(['labels' => $labels, 'counts' => $counts]);
    }

    public function getReportCountsByRw(): EloquentCollection
    {
        return Report::query()->whereHas('resident')->join('residents', 'reports.resident_id', '=', 'residents.id')
            ->join('rws', 'residents.rw_id', '=', 'rws.id')->select('rws.number as rw_number', DB::raw('count(*) as count'))
            ->groupBy('rws.number')->orderBy('rws.number')->get();
    }

    public function getStatusCounts(int $rwId = null): array
    {
        $query = Report::query()
            ->join('report_statuses', 'reports.id', '=', 'report_statuses.report_id')
            ->whereRaw('report_statuses.id = (SELECT MAX(id) FROM report_statuses WHERE report_id = reports.id)')
            ->whereHas('resident');

        if ($rwId) {
            $query->whereHas('resident', fn($q) => $q->where('rw_id', $rwId));
        }

        $statusCounts = $query->select('report_statuses.status', DB::raw('count(*) as count'))
            ->groupBy('report_statuses.status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $allStatuses = [
            ReportStatusEnum::DELIVERED->value => 0,
            ReportStatusEnum::IN_PROCESS->value => 0,
            ReportStatusEnum::COMPLETED->value => 0,
            ReportStatusEnum::REJECTED->value => 0,
        ];

        return array_merge($allStatuses, $statusCounts);
    }

    public function getReportCountsByRt(int $rwId): EloquentCollection
    {
        return Report::query()->join('residents', 'reports.resident_id', '=', 'residents.id')
            ->join('rts', 'residents.rt_id', '=', 'rts.id')->where('residents.rw_id', $rwId)
            ->select('rts.number as rt_number', DB::raw('count(*) as count'))
            ->groupBy('rts.number')->orderBy('rts.number')->get();
    }
}