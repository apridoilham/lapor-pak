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
        // Eager load relasi report untuk efisiensi
        return ReportStatus::with('report')->findOrFail($id);
    }

    // [PERBAIKAN] Tambahkan parameter ?int $actorId = null
    public function createReportStatus(array $data, ?int $actorId = null)
    {
        $reportStatus = ReportStatus::create($data);

        // [PENJELASAN] Salurkan actorId ke event. Jika actorId tidak diberikan (misalnya dari seeder/tinker),
        // gunakan Auth::id() sebagai fallback.
        ReportStatusUpdated::dispatch($reportStatus->report, $actorId ?? Auth::id());

        return $reportStatus;
    }

    // [PERBAIKAN] Tambahkan parameter ?int $actorId = null
    public function updateReportStatus(array $data, int $id, ?int $actorId = null)
    {
        $reportStatus = $this->getReportStatusById($id);
        $reportStatus->update($data);

        // [PENJELASAN] Salurkan actorId ke event dengan fallback yang sama.
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