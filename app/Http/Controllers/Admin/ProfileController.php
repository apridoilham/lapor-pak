<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Interfaces\AdminRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Menampilkan form untuk mengedit profil admin yang sedang login.
     */
    public function edit()
    {
        return view('pages.admin.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Menyimpan perubahan pada profil admin yang sedang login.
     */
    public function update(UpdateAdminProfileRequest $request)
    {
        $this->adminRepository->updateAdmin($request->validated(), Auth::id());

        Swal::success('Berhasil', 'Profil Anda berhasil diperbarui.');

        return redirect()->route('profile');
    }
}