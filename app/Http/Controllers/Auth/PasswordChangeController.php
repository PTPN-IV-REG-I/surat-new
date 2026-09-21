<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Ganti password wajib di login pertama (`must_change_password`). Tidak ada
 * alur "lupa password" lewat email (surat_users tidak punya email) — lihat
 * config/auth.php & arsitektur.md §7 poin 8. Reset akun yang lupa password
 * jadi tanggung jawab admin (Admin\UserController::resetPassword).
 */
class PasswordChangeController extends Controller
{
    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->string('password')),
            'must_change_password' => false,
        ])->save();

        return redirect()->route('dashboard')->with('status', 'Password berhasil diganti.');
    }
}
