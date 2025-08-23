<?php

namespace App\Exports;

use App\Interfaces\ReportRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $repository = app(ReportRepositoryInterface::class);
        
        return $repository->getFilteredReports($this->filters);
    }

    public function headings(): array
    {
        return [
            'Kode Laporan', 'Judul', 'Deskripsi Laporan', 'Nama Pelapor', 'Email Pelapor',
            'Kategori', 'Status Terakhir', 'Catatan Status Terakhir', 'Alamat',
            'Tanggal Dibuat', 'Tanggal Terakhir Diupdate',
        ];
    }

    public function map($report): array
    {
        return [
            $report->code,
            $report->title,
            $report->description,
            $report->resident->user->name,
            $report->resident->user->email,
            $report->reportCategory->name,
            $report->latestStatus ? $report->latestStatus->status->value : 'Baru',
            $report->latestStatus ? $report->latestStatus->description : '-',
            $report->address,
            $report->created_at->tz('Asia/Jakarta')->toDateTimeString(),
            $report->latestStatus ? $report->latestStatus->created_at->tz('Asia/Jakarta')->toDateTimeString() : $report->created_at->tz('Asia/Jakarta')->toDateTimeString(),
        ];
    }
}