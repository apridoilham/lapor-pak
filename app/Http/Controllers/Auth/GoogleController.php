<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert as Swal;

class GoogleController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(config('services.google.scopes'))
            ->stateless()
            ->redirect();
    }

    /**
     * Menangani callback dari Google setelah autentikasi.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cek apakah user sudah ada
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User sudah ada, update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);

                // Cek apakah ini email super-admin
                if ($googleUser->getEmail() === 'bsblapor@gmail.com') {
                    $user->assignRole('super-admin');
                } else {
                    // Assign role resident untuk user biasa
                    $user->assignRole('resident');

                    // Download dan simpan avatar
                    $avatarPath = null;
                    try {
                        $avatarUrl = $googleUser->getAvatar();
                        if ($avatarUrl) {
                            $avatarContents = file_get_contents($avatarUrl);
                            $avatarName = 'assets/avatar/' . Str::random(40) . '.jpg';
                            Storage::disk('public')->put($avatarName, $avatarContents);
                            $avatarPath = $avatarName;
                        }
                    } catch (\Exception $e) {
                        $avatarPath = null;
                    }

                    // Buat profil resident
                    $user->resident()->create([
                        'avatar'  => $avatarPath,
                        'address' => 'Alamat belum diatur',
                    ]);
                }
            }

            Auth::login($user, true);

            // Logika pengarahan setelah login
            if ($user->hasRole(['super-admin', 'admin'])) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('resident')) {
                $resident = $user->resident;

                // Cek kelengkapan profil
                if (!$resident || !$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur') {
                    Swal::info('Selamat Datang!', 'Silakan lengkapi data RT dan RW Anda terlebih dahulu.');
                    return redirect()->route('profile.edit');
                }
            }

            return redirect()->route('home');

        } catch (\Throwable $th) {
            // Tangani error jika autentikasi Google gagal
            return redirect()->route('login')->withErrors(['email' => 'Gagal melakukan autentikasi dengan Google. Silakan coba lagi.']);
        }
    }

    /**
     * Mengeluarkan pengguna dari aplikasi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}