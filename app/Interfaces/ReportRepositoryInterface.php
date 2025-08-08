<?php

namespace App\Interfaces;

interface ReportRepositoryInterface
{
    public function getAllReports();

    public function getLatesReports();

    // PERUBAHAN DI SINI: Tambahkan parameter int $residentId
    public function getReportByResidentId(int $residentId, ?string $status);

    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function getReportsByCategory(string $category);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);

    public function countStatusesByResidentId(int $residentId): array;
}