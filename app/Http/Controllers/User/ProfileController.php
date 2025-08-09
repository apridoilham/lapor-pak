<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Traits\FileUploadTrait; // <-- DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    use FileUploadTrait; // <-- DITAMBAHKAN

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
        $user = Auth::user();

        // Jika yang login adalah admin atau super-admin
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return view('pages.admin.profile', ['user' => $user]);
        }

        // Jika yang login adalah resident
        $stats = $this->reportRepository->countStatusesByResidentId($user->resident->id);

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

        Swal::success('Berhasil', 'Profil Anda berhasil diperbarui.');

        return redirect()->route('profile');
    }
}