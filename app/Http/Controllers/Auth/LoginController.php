<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoginRequest;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class LoginController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function store(StoreLoginRequest $request)
    {
        $credentials = $request->validated();

        if ($this->authRepository->login($credentials)) {
            $request->session()->regenerate(); // Tambahkan ini untuk keamanan
            
            $user = Auth::user();

            if ($user->hasAnyRole(['admin', 'super-admin'])) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->hasRole('resident')) {
                $resident = $user->resident;

                if (!$resident || !$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur') {
                    Swal::info('Selamat Datang!', 'Harap lengkapi data diri Anda terlebih dahulu untuk melanjutkan.');
                    return redirect()->route('profile.edit');
                }
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    public function logout(Request $request)
    {
        $this->authRepository->logout($request);
        return redirect()->route('login');
    }
}