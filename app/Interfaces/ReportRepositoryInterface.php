<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ReportRepositoryInterface
{
    public function getAllReports(Request $request);

    public function getLatestReports(Request $request = null);

    public function getReportByResidentId(int $residentId, ?string $status);
    
    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function getReportsByCategory(string $category);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);

    public function countStatusesByResidentId(int $residentId): array;

    public function getFilteredReports(array $filters);

    public function countReports(int $rwId = null): int;
    
    public function getCategoryReportCounts(int $rwId = null);

    public function getDailyReportCounts(int $rwId = null);

    public function getReportCountsByRw();

    public function getStatusCounts(int $rwId = null): array;
}