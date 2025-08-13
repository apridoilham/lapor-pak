<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Rt;
use App\Models\Rw;
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
        $user = Auth::user()->load('resident.rt', 'resident.rw');
        
        // Pastikan user memiliki data resident sebelum menghitung statistik
        if (!$user->resident) {
            // Ini adalah kasus darurat jika data resident tidak ada, arahkan ke edit
            Swal::error('Data Tidak Lengkap', 'Data kependudukan Anda tidak ditemukan, harap lengkapi profil.');
            return redirect()->route('profile.edit');
        }

        $stats = $this->reportRepository->countStatusesByResidentId($user->resident->id);

        return view('pages.app.profile', [
            'user' => $user, // Kirim variabel $user ke view
            'activeReportsCount' => $stats['active'],
            'completedReportsCount' => $stats['completed'],
            'rejectedReportsCount' => $stats['rejected'],
        ]);
    }

    public function edit()
    {
        $rts = Rt::orderBy('number')->get();
        $rws = Rw::orderBy('number')->get();

        return view('pages.app.profile-edit', [
            'user' => Auth::user(),
            'rts' => $rts,
            'rws' => $rws,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $validatedData = $request->validated();
        $resident = $request->user()->resident;

        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar', $resident->avatar)) {
            $validatedData['avatar'] = $path;
        }

        $this->residentRepository->updateResident($validatedData, $resident->id);

        Swal::success('Berhasil', 'Profil Anda berhasil diperbarui.');

        return redirect()->route('profile');
    }
}