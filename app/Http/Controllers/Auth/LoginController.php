<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoginRequest;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function index()
    {
        return view('pages.auth.login');
    }

    /**
     * PERUBAHAN DI SINI:
     * Menggunakan hasAnyRole untuk memeriksa 'admin' atau 'super-admin'.
     */
    public function store(StoreLoginRequest $request){
        $credentials = $request->validated();

        if ($this->authRepository->login($credentials)) {
            // Cek apakah pengguna memiliki peran 'admin' ATAU 'super-admin'
            if (Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        }

        return redirect()->route('login')->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    public function logout()
    {
        $this->authRepository->logout();

        return redirect()->route('login');
    }
}