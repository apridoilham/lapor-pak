<?php

namespace App\Interfaces;

use App\Enums\ReportStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReportRepositoryInterface
{
    public function getAllReportsForAdmin(Request $request, int $rwId = null, int $rtId = null): EloquentCollection;
    public function getAllReportsForUser(Request $request): LengthAwarePaginator;
    public function getLatestReportsForUser(Request $request): LengthAwarePaginator;
    public function getReportByResidentId(int $residentId, ?string $status): EloquentCollection;
    public function getReportById(int $id);
    public function getReportByCode(string $code);
    public function createReport(array $data);
    public function updateReport(array $data, int $id);
    public function deleteReport(int $id);
    public function countStatusesByResidentId(int $residentId): array;
    public function countByStatus(int $residentId, ReportStatusEnum $status): int;
    public function getFilteredReports(array $filters): EloquentCollection;
    public function countReports(int $rwId = null): int;
    public function getDailyReportCounts(int $rwId = null): Collection;
    public function getReportCountsByRw(): EloquentCollection;
    public function getStatusCounts(int $rwId = null): array;
    public function getLatestReportsForAdmin(?int $rwId = null, int $limit = 5): EloquentCollection;
    public function getCategoryReportCounts(int $rwId = null): EloquentCollection;
    public function getReportCountsByRt(int $rwId): EloquentCollection;
}