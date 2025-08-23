<?php

namespace App\Repositories;

use App\Events\ReportStatusUpdated;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Auth;

class ReportStatusRepository implements ReportStatusRepositoryInterface
{
    public function getAllReportStatuses()
    {
        return ReportStatus::all();
    }

    public function getReportStatusById(int $id)
    {
        return ReportStatus::with('report')->findOrFail($id);
    }

    public function createReportStatus(array $data, ?int $actorId = null)
    {
        $reportStatus = ReportStatus::create($data);

        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id());

        return $reportStatus;
    }

    public function updateReportStatus(array $data, int $id, ?int $actorId = null)
    {
        $reportStatus = $this->getReportStatusById($id);
        $reportStatus->update($data);

        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id());

        return $reportStatus;
    }

    public function deleteReportStatus(int $id)
    {
        $reportStatus = $this->getReportStatusById($id);
        $report = $reportStatus->report;
        $deleted = $reportStatus->delete();

        return $deleted;
    }
}