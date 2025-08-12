<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Http\Requests\UpdateResidentRequest;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class ResidentController extends Controller
{
    use FileUploadTrait;

    private ResidentRepositoryInterface $residentRepository;

    public function __construct(ResidentRepositoryInterface $residentRepository)
    {
        $this->residentRepository = $residentRepository;
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $rws = [];
        $rts = [];
        $residents = [];

        if ($user->hasRole('super-admin')) {
            $rwId = $request->input('rw');
            $rtId = $request->input('rt');
            $residents = $this->residentRepository->getAllResidents($rwId, $rtId);
            $rws = Rw::orderBy('number')->get();
        } else {
            $rtId = $request->input('rt');
            $residents = $this->residentRepository->getAllResidents($user->rw_id, $rtId);
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }

        return view('pages.admin.resident.index', compact('residents', 'rws', 'rts'));
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
        Swal::success('Berhasil', 'Data pelapor berhasil ditambahkan!');
        return redirect()->route('admin.resident.index');
    }

    public function show(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);
        
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
        Swal::success('Berhasil', 'Data pelapor berhasil diubah!');
        return redirect()->route('admin.resident.index');
    }

    public function destroy(string $id)
    {
        $this->residentRepository->deleteResident($id);
        Swal::success('Berhasil', 'Data pelapor berhasil dihapus!');
        return redirect()->route('admin.resident.index');
    }

    public function getReportsForDeletionAlert(Resident $resident)
    {
        $reports = $resident->reports()->with('latestStatus')->get()->map(function ($report) {
            return [
                'title' => \Str::limit($report->title, 30),
                'status' => $report->latestStatus ? $report->latestStatus->status->label() : 'Baru',
                'updated_at' => $report->latestStatus ? $report->latestStatus->updated_at->isoFormat('D MMM Y, HH:mm') : $report->created_at->isoFormat('D MMM Y, HH:mm'),
            ];
        });

        return response()->json($reports);
    }
}