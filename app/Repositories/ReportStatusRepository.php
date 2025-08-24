<?php

namespace App\Repositories;

use App\Events\ReportProgressDeleted;
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
        
        $oldStatusLabel = $reportStatus->status->label();
        $oldDescription = $reportStatus->description;
        $oldImage = $reportStatus->image;

        $reportStatus->update($data);
        $reportStatus->refresh();

        $changes = [];
        if ($reportStatus->status->label() !== $oldStatusLabel) {
            $changes['status'] = ['from' => $oldStatusLabel, 'to' => $reportStatus->status->label()];
        }
        
        if ($reportStatus->description !== $oldDescription) {
            $changes['description_updated'] = true;
        }

        if ($reportStatus->image !== $oldImage) {
            $changes['image_updated'] = true;
        }

        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id(), $changes);

        return $reportStatus;
    }

    public function deleteReportStatus(int $id, ?int $actorId = null)
    {
        $reportStatus = $this->getReportStatusById($id);
        $report = $reportStatus->report;
        $deletedStatusLabel = $reportStatus->status->label();
        $deleted = $reportStatus->delete();

        if ($deleted) {
            ReportProgressDeleted::dispatch($report, $actorId ?? Auth::id(), $deletedStatusLabel);
        }

        return $deleted;
    }
}