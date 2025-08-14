<?php

namespace App\Repositories;

use App\Events\ReportStatusUpdated;
use App\Interfaces\ReportStatusRepositoryInterface;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class ReportStatusRepository implements ReportStatusRepositoryInterface
{
    public function getAllReportStatuses()
    {
        return ReportStatus::all();
    }

    public function getReportStatusById(int $id)
    {
        // Eager load relasi report untuk efisiensi
        return ReportStatus::with('report')->findOrFail($id);
    }

    // TAMBAHKAN parameter baru: ?int $actorId = null
    public function createReportStatus(array $data, ?int $actorId = null)
    {
        $reportStatus = ReportStatus::create($data);

        // Salurkan actorId ke event
        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id());

        return $reportStatus;
    }

    // TAMBAHKAN parameter baru: ?int $actorId = null
    public function updateReportStatus(array $data, int $id, ?int $actorId = null)
    {
        $reportStatus = $this->getReportStatusById($id);
        $reportStatus->update($data);

        // Salurkan actorId ke event
        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id());

        return $reportStatus;
    }

    public function deleteReportStatus(int $id)
    {
        $reportStatus = $this->getReportStatusById($id);
        // Simpan report object sebelum dihapus untuk event (jika diperlukan)
        $report = $reportStatus->report;
        $deleted = $reportStatus->delete();

        // Mungkin Anda ingin dispatch event penghapusan juga di sini
        // ReportStatusDeleted::dispatch($report);

        return $deleted;
    }
}