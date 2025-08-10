<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Interfaces\AdminRepositoryInterface;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class AdminUserController extends Controller
{
    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
        $admins = $this->adminRepository->getAllAdmins();
        return view('pages.admin.user.index', compact('admins'));
    }

    public function create()
    {
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.user.create', compact('rws'));
    }

    public function store(StoreAdminRequest $request)
    {
        $this->adminRepository->createAdmin($request->validated());
        Swal::success('Berhasil', 'Admin baru berhasil ditambahkan.');
        return redirect()->route('admin.admin-user.index');
    }

    public function edit(User $admin_user)
    {
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.user.edit', ['admin' => $admin_user, 'rws' => $rws]);
    }

    public function update(UpdateAdminRequest $request, User $admin_user)
    {
        $this->adminRepository->updateAdmin($request->validated(), $admin_user->id);
        Swal::success('Berhasil', 'Data admin berhasil diperbarui.');
        return redirect()->route('admin.admin-user.index');
    }

    public function destroy(User $admin_user)
    {
        if ($admin_user->id === Auth::id()) {
            Swal::error('Gagal', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return back();
        }

        $this->adminRepository->deleteAdmin($admin_user->id);
        Swal::success('Berhasil', 'Admin berhasil dihapus.');
        return redirect()->route('admin.admin-user.index');
    }
}