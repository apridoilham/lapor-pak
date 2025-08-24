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
            'No Telepon Pelapor', 'Alamat Pelapor', 'RT Pelapor', 'RW Pelapor',
            'Kategori', 'Status Terakhir', 'Catatan Status Terakhir', 'Lokasi Kejadian',
            'Tanggal Dibuat', 'Tanggal Terakhir Diupdate',
        ];
    }

    public function map($report): array
    {
        return [
            $report->code,
            $report->title,
            $report->description,
            optional($report->resident->user)->name ?? 'N/A',
            optional($report->resident->user)->email ?? 'N/A',
            optional($report->resident)->phone ?? '-',
            optional($report->resident)->address ?? '-',
            optional($report->resident->rt)->number ?? '-',
            optional($report->resident->rw)->number ?? '-',
            optional($report->reportCategory)->name ?? 'N/A',
            optional($report->latestStatus)->status->label() ?? 'Baru',
            optional($report->latestStatus)->description ?? '-',
            $report->address,
            $report->created_at->tz('Asia/Jakarta')->toDateTimeString(),
            optional($report->latestStatus)->created_at ? $report->latestStatus->created_at->tz('Asia/Jakarta')->toDateTimeString() : $report->created_at->tz('Asia/Jakarta')->toDateTimeString(),
        ];
    }
}