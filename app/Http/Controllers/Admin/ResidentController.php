<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Interfaces\ResidentRepositoryInterface;
use App\Traits\FileUploadTrait; // <-- DITAMBAHKAN
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ResidentController extends Controller
{
    use FileUploadTrait; // <-- DITAMBAHKAN

    private ResidentRepositoryInterface $residentRepository;

    public function __construct(ResidentRepositoryInterface $residentRepository)
    {
        $this->residentRepository = $residentRepository;
    }
    
    public function index()
    {
        $residents = $this->residentRepository->getAllResidents();

        return view('pages.admin.resident.index', compact('residents'));
    }

    public function create()
    {
        return view('pages.admin.resident.create');
    }

    public function store(StoreResidentRequest $request)
    {
        $data = $request->validated();

        // PERUBAHAN DI SINI
        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }

        $this->residentRepository->createResident($data);

        Swal::success('Success', 'Data masyarakat berhasil ditambahkan!')->timerProgressBar();

        return redirect()->route('admin.resident.index');
    }

    public function show(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);

        return view('pages.admin.resident.show', compact('resident'));
    }

    public function edit(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);

        return view('pages.admin.resident.edit', compact('resident'));
    }

    public function update(UpdateResidentRequest $request, string $id)
    {
        $data = $request->validated();

        // PERUBAHAN DI SINI
        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }

        $this->residentRepository->updateResident($data, $id);

        Swal::success('Success', 'Data masyarakat berhasil diubah!')->timerProgressBar();

        return redirect()->route('admin.resident.index');
    }

    public function destroy(string $id)
    {
        $this->residentRepository->deleteResident($id);

        Swal::success('Success', 'Data masyarakat berhasil dihapus!')->timerProgressBar();

        return redirect()->route('admin.resident.index');
    }
}