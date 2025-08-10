<?php

namespace App\Repositories;

use App\Enums\ReportStatusEnum;
use App\Enums\ReportVisibilityEnum;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                            $q3->where('rt_id', $user->resident->rt_id);
                        });
                });
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('visibility', ReportVisibilityEnum::PRIVATE)
                       ->where('resident_id', $user->resident->id);
                });
            }
        });
    }

    public function getAllReports(Request $request)
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus');
        
        if (!Auth::user() || !Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            $this->applyVisibilityFilter($query);
        }

        if ($category = $request->input('category')) {
            $query->whereHas('reportCategory', function (Builder $q) use ($category) {
                $q->where('name', $category);
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

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('resident.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->get();
    }

    public function getLatesReports(Request $request = null)
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus');
        
        if ($request) {
            $this->applyVisibilityFilter($query);

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
        }
        
        return $query->latest()->take(5)->get();
    }
    
    public function getReportByResidentId(int $residentId, ?string $status)
    {
        $query = Report::where('resident_id', $residentId)->with('latestStatus');

        if ($status) {
            $query->whereHas('latestStatus', function (Builder $query) use ($status) {
                $query->where('status', $status);
            });
        }

        return $query->latest()->get();
    }

    public function getReportById(int $id)
    {
        return Report::with('resident', 'reportCategory', 'reportStatuses')->findOrFail($id);
    }

    public function getReportByCode(string $code)
    {
        return Report::with('resident.user', 'resident.rt', 'resident.rw', 'reportCategory', 'reportStatuses')->where('code', $code)->firstOrFail();
    }

    public function getReportsByCategory(string $categoryName)
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus')
            ->whereHas('reportCategory', function (Builder $query) use ($categoryName) {
                $query->where('name', $categoryName);
            });
            
        $this->applyVisibilityFilter($query);
        
        return $query->latest()->get();
    }

    public function createReport(array $data)
    {
        $report = Report::create($data);

        $report->reportStatuses()->create([
            'status' => ReportStatusEnum::DELIVERED,
            'description' => 'Laporan berhasil diterima oleh sistem dan akan segera diproses.',
        ]);

        return $report;
    }

    public function updateReport(array $data, int $id)
    {
        return Report::findOrFail($id)->update($data);
    }

    public function deleteReport(int $id)
    {
        return Report::findOrFail($id)->delete();
    }

    public function countStatusesByResidentId(int $residentId): array
    {
        $active = Report::where('resident_id', $residentId)
            ->whereHas('latestStatus', function (Builder $query) {
                $query->whereIn('status', [ReportStatusEnum::DELIVERED, ReportStatusEnum::IN_PROCESS]);
            })
            ->count();

        $completed = Report::where('resident_id', $residentId)
            ->whereHas('latestStatus', function (Builder $query) {
                $query->where('status', ReportStatusEnum::COMPLETED);
            })
            ->count();

        $rejected = Report::where('resident_id', $residentId)
            ->whereHas('latestStatus', function (Builder $query) {
                $query->where('status', ReportStatusEnum::REJECTED);
            })
            ->count();

        return [
            'active' => $active,
            'completed' => $completed,
            'rejected' => $rejected,
        ];
    }

    public function getFilteredReports(array $filters)
    {
        $query = Report::with('resident.user', 'reportCategory', 'latestStatus');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['resident_id'])) {
            $query->where('resident_id', $filters['resident_id']);
        }

        if (!empty($filters['report_category_id'])) {
            $query->where('report_category_id', $filters['report_category_id']);
        }

        if (!empty($filters['status'])) {
            $query->whereHas('latestStatus', function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            });
        }

        return $query->get();
    }
}