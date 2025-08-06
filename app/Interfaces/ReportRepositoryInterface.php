<?php

namespace App\Interfaces;

interface ReportRepositoryInterface
{
    public function getAllReports();

    public function getLatesReports();
    
    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);
}