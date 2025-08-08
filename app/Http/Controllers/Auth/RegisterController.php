<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Interfaces\ResidentRepositoryInterface;
use App\Traits\FileUploadTrait; // <-- DITAMBAHKAN
use RealRashid\SweetAlert\Facades\Alert as Swal;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use FileUploadTrait; // <-- DITAMBAHKAN

    private ResidentRepositoryInterface $residentRepository;

    public function __construct(ResidentRepositoryInterface $residentRepository)
    {
        $this->residentRepository = $residentRepository;
    }

    public function index()
    {
        return view('pages.auth.register');
    }

    public function store(StoreResidentRequest $request)
    {
        $data = $request->validated();

        // PERUBAHAN DI SINI
        if ($path = $this->handleFileUpload($request, 'avatar', 'assets/avatar')) {
            $data['avatar'] = $path;
        }

        $this->residentRepository->createResident($data);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login.');
    }
}