<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ProfileController extends Controller
{
    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
        return view('pages.admin.profile', [
            'user' => Auth::user()->load('rw'),
        ]);
    }

    public function edit()
    {
        return view('pages.admin.profile.edit', [
            'user' => Auth::user()->load('rw'),
        ]);
    }

    public function update(UpdateAdminProfileRequest $request)
    {
        $this->adminRepository->updateAdmin($request->validated(), Auth::id());

        Swal::success('Berhasil', 'Profil Anda berhasil diperbarui.');

        return redirect()->route('admin.profile.index');
    }
}