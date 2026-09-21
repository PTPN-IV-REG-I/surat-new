<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa ganti password di login pertama untuk seluruh akun hasil migrasi
 * (`surat_users.must_change_password=true`) — password lama TIDAK dibawa
 * sama sekali karena 82% identik dengan username (arsitektur.md §7 poin 8).
 * Route ganti password sendiri dikecualikan supaya tidak infinite redirect.
 */
class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->must_change_password && ! $request->routeIs('password.change', 'password.update', 'logout')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
