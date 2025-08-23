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
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(config('services.google.scopes'))
            ->stateless()
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);

                if ($googleUser->getEmail() === 'bsblapor@gmail.com') {
                    $user->assignRole('super-admin');
                } else {
                    $user->assignRole('resident');

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

                    $user->resident()->create([
                        'avatar'  => $avatarPath,
                        'address' => 'Alamat belum diatur',
                    ]);
                }
            }

            Auth::login($user, true);

            if ($user->hasRole(['super-admin', 'admin'])) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('resident')) {
                $resident = $user->resident;

                if (!$resident || !$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur') {
                    Swal::info('Selamat Datang!', 'Silakan lengkapi data RT dan RW Anda terlebih dahulu.');
                    return redirect()->route('profile.edit');
                }
            }

            return redirect()->route('home');

        } catch (\Throwable $th) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal melakukan autentikasi dengan Google. Silakan coba lagi.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}