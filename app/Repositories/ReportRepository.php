<?php

namespace App\Repositories;

use App\Enums\ReportStatusEnum;
use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportRepository implements ReportRepositoryInterface
{
    public function getAllReports()
    {
        return Report::with('resident', 'reportCategory', 'latestStatus')->latest()->get();
    }

    public function getLatesReports()
    {
        return Report::with('resident', 'reportCategory', 'latestStatus')->latest()->take(5)->get();
    }

    /**
     * PERUBAHAN DI SINI: Method ini sekarang menerima $residentId
     * dan tidak lagi menggunakan Auth::user()
     */
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
        return Report::with('resident', 'reportCategory', 'reportStatuses')->where('code', $code)->firstOrFail();
    }

    public function getReportsByCategory(string $categoryName)
    {
        return Report::with('resident', 'reportCategory', 'latestStatus')
            ->whereHas('reportCategory', function (Builder $query) use ($categoryName) {
                $query->where('name', $categoryName);
            })->latest()->get();
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
}