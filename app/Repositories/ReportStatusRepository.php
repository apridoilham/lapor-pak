<?php

namespace App\Repositories;

use App\Events\ReportStatusUpdated; // <-- DITAMBAHKAN
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Models\ReportStatus;


class ReportStatusRepository implements ReportStatusRepositoryInterface
{
    public function getAllReportStatuses()
    {
        return ReportStatus::all();
    }

    public function getReportStatusById(int $id)
    {
        return ReportStatus::where('id', $id)->first();
    }

    public function createReportStatus(array $data)
    {
        $reportStatus = ReportStatus::create($data);

        // Memicu event setelah status baru dibuat
        ReportStatusUpdated::dispatch($reportStatus->report);

        return $reportStatus;
    }

    public function updateReportStatus(array $data, int $id)
    {
        $reportStatus = $this->getReportStatusById($id);
        $reportStatus->update($data);

        // Memicu event setelah status diupdate
        ReportStatusUpdated::dispatch($reportStatus->report);

        return $reportStatus;
    }

    public function deleteReportStatus(int $id)
    {
        $reportStatus = $this->getReportStatusById($id);

        return $reportStatus->delete();
    }
}