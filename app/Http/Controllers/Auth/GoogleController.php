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

            $rawAvatarUrl = $googleUser->getAvatar();
            $localAvatarPath = null;

            if (!empty($rawAvatarUrl) && strpos($rawAvatarUrl, '/picture/0') === false) {
                try {
                    $avatarContents = @file_get_contents($rawAvatarUrl);
                    if ($avatarContents) {
                        $avatarName = 'assets/avatar/' . Str::random(40) . '.jpg';
                        Storage::disk('public')->put($avatarName, $avatarContents);
                        $localAvatarPath = $avatarName;
                    }
                } catch (\Exception $e) {
                    $localAvatarPath = null;
                }
            }

            if ($user) {
                $updateData = ['google_id' => $googleUser->getId()];

                if ($user->name === 'Super Admin Haeritage 31' && empty($user->avatar)) {
                    $updateData['name'] = $googleUser->getName();
                    $updateData['avatar'] = $localAvatarPath;
                }
                
                $user->update($updateData);

            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $localAvatarPath,
                ]);

                if ($googleUser->getEmail() === config('app.super_admin_email', 'bsblapor@gmail.com')) {
                    $user->assignRole('super-admin');
                } else {
                    $user->assignRole('resident');
                    $user->resident()->create([
                        'avatar' => $user->avatar,
                        'address' => '',
                    ]);
                }
            }

            Auth::login($user, true);

            if ($user->hasRole(['super-admin', 'admin'])) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('resident')) {
                $resident = $user->resident;
                if (!$resident || !$resident->rt_id || !$resident->rw_id || empty(trim($resident->address))) {
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