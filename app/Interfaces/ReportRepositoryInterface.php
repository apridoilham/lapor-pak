<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

interface ReportRepositoryInterface
{
    public function getAllReportsForAdmin(Request $request, int $rwId = null, int $rtId = null): EloquentCollection;

    public function getAllReportsForUser(Request $request): EloquentCollection;

    public function getLatestReportsForAdmin(?int $rwId = null, int $limit = 5): EloquentCollection;

    public function getLatestReportsForUser(Request $request): EloquentCollection;

    public function getReportByResidentId(int $residentId, ?string $status): EloquentCollection;
    
    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);

    public function countStatusesByResidentId(int $residentId): array;

    public function getFilteredReports(array $filters): EloquentCollection;

    public function countReports(int $rwId = null): int;
    
    public function getCategoryReportCounts(int $rwId = null): EloquentCollection;

    // Perbaikan: Return type sekarang adalah Illuminate\Support\Collection
    public function getDailyReportCounts(int $rwId = null): Collection;

    public function getReportCountsByRw(): EloquentCollection;

    public function getStatusCounts(int $rwId = null): array;

    public function getReportCountsByRt(int $rwId): EloquentCollection;
}