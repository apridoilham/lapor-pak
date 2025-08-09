<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Rt;
use App\Models\Rw;
use App\Traits\FileUploadTrait;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ResidentController extends Controller
{
    use FileUploadTrait;

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
        $rts = Rt::orderBy('number')->get();
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.resident.create', compact('rts', 'rws'));
    }

    public function store(StoreResidentRequest $request)
    {
        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }
        $this->residentRepository->createResident($data);
        Swal::success('Berhasil', 'Data masyarakat berhasil ditambahkan!');
        return redirect()->route('admin.resident.index');
    }

    /**
     * PERUBAHAN DI SINI:
     * Muat relasi 'reports' saat mengambil data resident.
     */
    public function show(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);
        
        // Memuat data laporan yang berelasi dengan resident ini
        $resident->load(['reports.latestStatus', 'reports.reportCategory']);

        return view('pages.admin.resident.show', compact('resident'));
    }

    public function edit(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);
        $rts = Rt::orderBy('number')->get();
        $rws = Rw::orderBy('number')->get();
        return view('pages.admin.resident.edit', compact('resident', 'rts', 'rws'));
    }

    public function update(UpdateResidentRequest $request, string $id)
    {
        $data = $request->validated();
        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }
        $this->residentRepository->updateResident($data, $id);
        Swal::success('Berhasil', 'Data masyarakat berhasil diubah!');
        return redirect()->route('admin.resident.index');
    }

    public function destroy(string $id)
    {
        $this->residentRepository->deleteResident($id);
        Swal::success('Berhasil', 'Data masyarakat berhasil dihapus!');
        return redirect()->route('admin.resident.index');
    }
}