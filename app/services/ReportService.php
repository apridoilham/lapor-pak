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

    public function createReportForUser(array $validatedData, User $user, UploadedFile $image): \App\Models\Report
    {
        $validatedData['code'] = config('report.code_prefix.user') . mt_rand(100000, 999999);
        $validatedData['resident_id'] = $user->resident->id;
        $imagePath = $this->storeFile($image, 'assets/report/image');
        $validatedData['image'] = $imagePath;

        return $this->reportRepository->createReport($validatedData);
    }

    protected function storeFile(UploadedFile $file, string $path): string
    {
        return $file->store($path, 'public');
    }
}