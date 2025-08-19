<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
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

        $query = Resident::with(['user', 'rt', 'rw'])->withCount('reports');

        if ($user->hasRole('super-admin')) {
            $rwId = $request->input('rw');
            $rtId = $request->input('rt');
            if ($rtId) { $query->where('rt_id', $rtId); } 
            elseif ($rwId) { $query->where('rw_id', $rwId); }
            $rws = Rw::orderBy('number')->get();
        } else {
            $rtId = $request->input('rt');
            $query->where('rw_id', $user->rw_id);
            if ($rtId) { $query->where('rt_id', $rtId); }
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }

        $residents = $query->paginate(9)->withQueryString();

        return view('pages.admin.resident.index', compact('residents', 'rws', 'rts'));
    }

    public function show(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id)->load(['reports.latestStatus', 'reports.reportCategory', 'reports.resident.user']);

        // [PERBAIKAN] Mengembalikan logika untuk menghitung statistik
        $stats = [
            'total' => $resident->reports->count(),
            'in_process' => $resident->reports->where('latestStatus.status', ReportStatusEnum::IN_PROCESS)->count(),
            'completed' => $resident->reports->where('latestStatus.status', ReportStatusEnum::COMPLETED)->count(),
            'rejected' => $resident->reports->where('latestStatus.status', ReportStatusEnum::REJECTED)->count(),
        ];

        return view('pages.admin.resident.show', compact('resident', 'stats'));
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