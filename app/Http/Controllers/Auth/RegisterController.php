<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Interfaces\ResidentRepositoryInterface;
use App\Models\Rt;
use App\Models\Rw;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class RegisterController extends Controller
{
    use FileUploadTrait;

    private ResidentRepositoryInterface $residentRepository;

    public function __construct(ResidentRepositoryInterface $residentRepository)
    {
        $this->residentRepository = $residentRepository;
    }

    public function index()
    {
        $rts = Rt::orderBy('number')->get();
        $rws = Rw::orderBy('number')->get();
        return view('pages.auth.register', compact('rts', 'rws'));
    }

    public function store(StoreResidentRequest $request)
    {
        $data = $request->validated();

        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }

        $this->residentRepository->createResident($data);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}