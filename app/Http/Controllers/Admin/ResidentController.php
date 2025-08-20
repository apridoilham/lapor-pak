<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Logika Filter
        if ($user->hasRole('super-admin')) {
            $rwId = $request->input('rw');
            $rtId = $request->input('rt');
            if ($rtId) {
                $query->where('rt_id', $rtId);
            } elseif ($rwId) {
                $query->where('rw_id', $rwId);
            }
            $rws = Rw::orderBy('number')->get();
        } else {
            $rtId = $request->input('rt');
            $query->where('rw_id', $user->rw_id);
            if ($rtId) {
                $query->where('rt_id', $rtId);
            }
            $rts = Rt::where('rw_id', $user->rw_id)->orderBy('number')->get();
        }

        // Logika Pengurutan
        $sortBy = $request->query('sort', 'latest');
        switch ($sortBy) {
            case 'name_asc':
                $query->join('users', 'residents.user_id', '=', 'users.id')->orderBy('users.name', 'asc')->select('residents.*');
                break;
            case 'name_desc':
                $query->join('users', 'residents.user_id', '=', 'users.id')->orderBy('users.name', 'desc')->select('residents.*');
                break;
            case 'reports_desc':
                $query->orderBy('reports_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('residents.created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('residents.created_at', 'desc');
                break;
        }

        $residents = $query->paginate(9)->withQueryString();

        return view('pages.admin.resident.index', compact('residents', 'rws', 'rts'));
    }

    public function show(string $id)
    {
        $resident = $this->residentRepository->getResidentById($id);
        $resident->load(['reports.latestStatus', 'reports.reportCategory']);

        $stats = [
            'total' => $resident->reports->count(),
            'in_process' => $resident->reports->where('latestStatus.status', \App\Enums\ReportStatusEnum::IN_PROCESS)->count(),
            'completed' => $resident->reports->where('latestStatus.status', \App\Enums\ReportStatusEnum::COMPLETED)->count(),
            'rejected' => $resident->reports->where('latestStatus.status', \App\Enums\ReportStatusEnum::REJECTED)->count(),
        ];

        return view('pages.admin.resident.show', compact('resident', 'stats'));
    }
}