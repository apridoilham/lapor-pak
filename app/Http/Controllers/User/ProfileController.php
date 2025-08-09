<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ResidentRepositoryInterface $residentRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ResidentRepositoryInterface $residentRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->residentRepository = $residentRepository;
    }

    /**
     * PERUBAHAN DI SINI:
     * Membedakan tampilan profil untuk admin dan resident.
     */
    public function index()
    {
        $user = Auth::user();

        // Jika yang login adalah admin atau super-admin
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            // Arahkan ke view profil khusus admin
            return view('pages.admin.profile', ['user' => $user]);
        }

        // Jika yang login adalah resident (logika yang sudah ada)
        $stats = $this->reportRepository->countStatusesByResidentId($user->resident->id);

        return view('pages.app.profile', [
            'activeReportsCount' => $stats['active'],
            'completedReportsCount' => $stats['completed'],
            'rejectedReportsCount' => $stats['rejected'],
        ]);
    }

    /**
     * Method edit ini hanya untuk resident.
     * Admin akan punya cara edit profilnya sendiri di menu Manajemen Admin.
     */
    public function edit()
    {
        return view('pages.app.profile-edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Method update ini hanya untuk resident.
     */
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