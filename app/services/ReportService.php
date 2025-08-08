<?php

namespace App\Services;

use App\Interfaces\ReportRepositoryInterface;
use App\Traits\FileUploadTrait;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ReportService
{
    use FileUploadTrait;

    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Menangani seluruh logika pembuatan laporan baru oleh pengguna.
     *
     * @param array $validatedData Data yang sudah divalidasi dari Form Request.
     * @param User $user Pengguna yang sedang login.
     * @return \App\Models\Report
     */
    public function createReportForUser(array $validatedData, User $user): \App\Models\Report
    {
        // 1. Tambahkan data yang tidak ada di form
        $validatedData['code'] = config('report.code_prefix.user') . mt_rand(100000, 999999);
        $validatedData['resident_id'] = $user->resident->id;

        // 2. Unggah dan simpan gambar (menggunakan Trait yang sudah kita buat)
        // Catatan: StoreReportRequest sudah memastikan 'image' ada.
        $imagePath = request()->file('image')->store('assets/report/image', 'public');
        $validatedData['image'] = $imagePath;

        // 3. Buat laporan melalui repository
        return $this->reportRepository->createReport($validatedData);
    }
}