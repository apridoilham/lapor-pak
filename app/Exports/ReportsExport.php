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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Gunakan App::make() untuk memanggil repository dari dalam kelas ini
        $repository = app(ReportRepositoryInterface::class);
        
        // Ambil data yang sudah difilter
        return $repository->getFilteredReports($this->filters);
    }

    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'Kode Laporan', 'Judul', 'Deskripsi Laporan', 'Nama Pelapor', 'Email Pelapor',
            'Kategori', 'Status Terakhir', 'Catatan Status Terakhir', 'Alamat',
            'Tanggal Dibuat', 'Tanggal Terakhir Diupdate',
        ];
    }

    /**
     * Memetakan data dari setiap baris laporan ke kolom yang sesuai.
     */
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