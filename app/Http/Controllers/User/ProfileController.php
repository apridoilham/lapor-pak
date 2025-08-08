<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    use FileUploadTrait;

    private ReportRepositoryInterface $reportRepository;
    private ResidentRepositoryInterface $residentRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ResidentRepositoryInterface $residentRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->residentRepository = $residentRepository;
    }

    public function index()
    {
        $stats = $this->reportRepository->countStatusesByResidentId(Auth::user()->resident->id);

        return view('pages.app.profile', [
            'activeReportsCount' => $stats['active'],
            'completedReportsCount' => $stats['completed'],
            'rejectedReportsCount' => $stats['rejected'],
        ]);
    }

    public function edit()
    {
        return view('pages.app.profile-edit', [
            'user' => Auth::user()
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $validatedData = $request->validated();
        $resident = $request->user()->resident;

        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $validatedData['avatar'] = $path;
        }

        $this->residentRepository->updateResident($validatedData, $resident->id);

        // ▼▼▼ TAMBAHKAN BARIS INI UNTUK MENGIRIM NOTIFIKASI SUKSES ▼▼▼
        Swal::success('Berhasil', 'Profil Anda telah berhasil diperbarui.');

        return redirect()->route('profile');
    }
}