<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert as Swal;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->hasAnyRole(['admin', 'super-admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user && $user->hasRole('resident')) {
            $resident = $user->resident;
            $isProfileIncomplete = !$resident || !$resident->rt_id || !$resident->rw_id || empty(trim($resident->address));

            if ($isProfileIncomplete) {
                if (!$request->routeIs('profile') && !$request->routeIs('profile.edit') && !$request->routeIs('profile.update') && !$request->routeIs('logout')) {

                    Swal::warning('Profil Belum Lengkap', 'Harap lengkapi data diri Anda terlebih dahulu untuk melanjutkan.');

                    return redirect()->route('profile.edit');
                }
            }
        }

        return $next($request);
    }
}