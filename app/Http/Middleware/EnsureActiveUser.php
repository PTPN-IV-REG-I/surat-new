<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menggantikan pola legacy: akun dinonaktifkan tidak lewat DELETE, cukup
 * `surat_users.is_active=false` (arsitektur.md §9). Dicek di setiap request,
 * bukan hanya saat login, supaya akun yang dinonaktifkan di tengah sesi
 * langsung ter-logout.
 */
class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        return $next($request);
    }
}
