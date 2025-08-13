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

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail(),
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                ]
            );

            // Buat profil resident jika pengguna baru
            if ($user->wasRecentlyCreated) {
                $user->assignRole('resident');

                // Logika untuk mengunduh foto profil dari Google dan menyimpannya secara lokal
                $avatarPath = null;
                try {
                    // Ambil URL avatar dari Google
                    $avatarUrl = $googleUser->getAvatar();
                    if ($avatarUrl) {
                        // Dapatkan konten gambar
                        $avatarContents = file_get_contents($avatarUrl);
                        // Buat nama file yang unik
                        $avatarName = 'assets/avatar/' . Str::random(40) . '.jpg';
                        // Simpan file ke dalam storage publik Anda
                        Storage::disk('public')->put($avatarName, $avatarContents);
                        // Simpan path lokalnya
                        $avatarPath = $avatarName;
                    }
                } catch (\Exception $e) {
                    // Jika gagal download, biarkan avatar kosong (null)
                    // Anda bisa menambahkan log error di sini jika perlu
                    $avatarPath = null;
                }

                $user->resident()->create([
                    'avatar'  => $avatarPath, // Simpan path lokal, bukan URL Google
                    'address' => 'Alamat belum diatur',
                ]);
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