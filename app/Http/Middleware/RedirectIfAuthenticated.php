<?php

namespace App\Http;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Jika user punya peran admin/super-admin, arahkan ke dashboard admin
                if ($user->hasAnyRole(['admin', 'super-admin'])) {
                    return redirect()->route('admin.dashboard');
                }
                
                // Jika tidak, arahkan ke halaman utama pengguna
                return redirect('/');
            }
        }

        return $next($request);
    }
}