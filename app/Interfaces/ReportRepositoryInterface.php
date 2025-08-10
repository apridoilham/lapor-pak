<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ReportRepositoryInterface
{
    public function getAllReports(Request $request);

    public function getLatesReports($rwId = null, $rtId = null);

    public function getReportByResidentId(int $residentId, ?string $status);
    
    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function getReportsByCategory(string $category);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);

    public function countStatusesByResidentId(int $residentId): array;

    public function getFilteredReports(array $filters);
}