<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->hasRole('resident')) {
            $resident = $user->resident;
            $isProfileIncomplete = !$resident || !$resident->rt_id || !$resident->rw_id || $resident->address === 'Alamat belum diatur';

            if ($isProfileIncomplete) {
                // Halaman yang diizinkan adalah: lihat profil, edit profil, proses update, dan logout.
                // TAMBAHKAN !$request->routeIs('profile') DI SINI
                if (!$request->routeIs('profile') && !$request->routeIs('profile.edit') && !$request->routeIs('profile.update') && !$request->routeIs('logout')) {

                    Swal::warning('Profil Belum Lengkap', 'Harap lengkapi data diri Anda terlebih dahulu untuk melanjutkan.');

                    return redirect()->route('profile.edit');
                }
            }
        }

        return $next($request);
    }
}