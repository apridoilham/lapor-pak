<?php

namespace App\Services;

use App\Interfaces\ReportRepositoryInterface;
use App\Traits\FileUploadTrait;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
     * @param UploadedFile $image File gambar yang diunggah.
     * @return \App\Models\Report
     */
    public function createReportForUser(array $validatedData, User $user, UploadedFile $image): \App\Models\Report
    {
        // 1. Tambahkan data yang tidak ada di form
        $validatedData['code'] = config('report.code_prefix.user') . mt_rand(100000, 999999);
        $validatedData['resident_id'] = $user->resident->id;

        // --- PERBAIKAN DI SINI ---
        // Hapus baris di bawah ini, karena $validatedData['visibility'] sudah berupa string yang benar.
        // $validatedData['visibility'] = $validatedData['visibility']->value;

        // 2. Unggah dan simpan gambar (menggunakan Trait yang sudah kita buat)
        // Pastikan Anda memanggil trait dengan benar, atau gunakan logika upload langsung
        $imagePath = $this->storeFile($image, 'assets/report/image');
        $validatedData['image'] = $imagePath;

        // 3. Buat laporan melalui repository
        return $this->reportRepository->createReport($validatedData);
    }

    /**
     * Menyimpan file yang diunggah ke disk.
     * (Asumsi dari FileUploadTrait)
     *
     * @param UploadedFile $file Instance file yang diunggah.
     * @param string $path Direktori penyimpanan di dalam 'storage/app/public'.
     * @return string Path file yang disimpan.
     */
    protected function storeFile(UploadedFile $file, string $path): string
    {
        return $file->store($path, 'public');
    }
}