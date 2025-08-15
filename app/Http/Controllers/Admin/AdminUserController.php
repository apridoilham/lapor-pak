<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Interfaces\AdminRepositoryInterface;
use App\Models\Rw;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class AdminUserController extends Controller
{
    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Menampilkan daftar semua admin.
     */
    public function index()
    {
        $admins = $this->adminRepository->getAllAdmins();
        return view('pages.admin.user.index', compact('admins'));
    }

    /**
     * Menampilkan form untuk membuat admin baru.
     */
    public function create()
    {
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.user.create', compact('rws'));
    }

    /**
     * Menyimpan admin baru ke database.
     */
    public function store(StoreAdminRequest $request)
    {
        $this->adminRepository->createAdmin($request->validated());

        Swal::success('Berhasil', 'Admin baru berhasil ditambahkan.');
        return redirect()->route('admin.admin-user.index');
    }

    /**
     * Menampilkan detail seorang admin.
     */
    public function show(User $admin)
    {
        // Pastikan kita hanya menampilkan user dengan role admin
        if (!$admin->hasRole('admin')) {
            abort(404);
        }
        $admin->load('rw');
        return view('pages.admin.user.show', ['admin' => $admin]);
    }

    /**
     * Menampilkan form untuk mengedit admin.
     */
    public function edit(User $admin)
    {
        if (!$admin->hasRole('admin')) {
            abort(404);
        }
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.user.edit', [
            'admin' => $admin,
            'rws' => $rws,
        ]);
    }

    /**
     * Memperbarui data admin di database.
     */
    public function update(UpdateAdminRequest $request, User $admin)
    {
        $this->adminRepository->updateAdmin($request->validated(), $admin->id);

        Swal::success('Berhasil', 'Data admin berhasil diperbarui.');
        return redirect()->route('admin.admin-user.index');
    }

    /**
     * Menghapus admin dari database.
     */
    public function destroy(User $admin)
    {
        $this->adminRepository->deleteAdmin($admin->id);

        Swal::success('Berhasil', 'Admin berhasil dihapus.');
        return redirect()->route('admin.admin-user.index');
    }
}