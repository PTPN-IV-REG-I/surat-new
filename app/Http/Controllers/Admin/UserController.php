<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Director;
use App\Models\SuratUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

/**
 * Kelola akun surat_users + role Spatie. Menggantikan fitur admin lama yang
 * hanya bisa lihat password plaintext (`passurat.asp`, sengaja TIDAK dibawa
 * — lihat arsitektur-surat-lama.md §3.1). Reset password di sini SELALU
 * generate ulang, tidak pernah menampilkan/menyimpan password lama.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = SuratUser::query()
            ->with(['roles', 'director', 'department'])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = "%{$request->string('q')}%";
                $q->where('username', 'like', $term)->orWhere('name', 'like', $term);
            }))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'q' => $request->string('q'),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', $this->formOptions() + ['user' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);

        $randomPassword = Str::password(12);

        $user = SuratUser::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'director_id' => $data['director_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'nik' => $data['nik'] ?? null,
            'password' => Hash::make($randomPassword),
            'is_active' => $request->boolean('is_active', true),
            'must_change_password' => true,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('status', "Akun '{$user->username}' dibuat. Password sementara: {$randomPassword} (wajib diganti saat login pertama — catat sekarang, tidak ditampilkan lagi).");
    }

    public function edit(SuratUser $user)
    {
        $user->load('roles');

        return view('admin.users.form', $this->formOptions() + ['user' => $user]);
    }

    public function update(Request $request, SuratUser $user): RedirectResponse
    {
        $data = $this->validateUser($request, $user->id);

        $user->update([
            'username' => $data['username'],
            'name' => $data['name'],
            'director_id' => $data['director_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'nik' => $data['nik'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')->with('status', "Akun '{$user->username}' diperbarui.");
    }

    /**
     * Nonaktifkan akun — BUKAN hapus fisik. `created_by` di letters/
     * letter_divisions/dispositions merujuk ke akun ini, jadi physical
     * delete akan merusak riwayat data (arsitektur.md §9).
     */
    public function destroy(SuratUser $user): RedirectResponse
    {
        if ($user->username === 'legacy-migration') {
            return back()->withErrors(['username' => 'Akun sistem tidak boleh dinonaktifkan lewat sini.']);
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')->with('status', "Akun '{$user->username}' dinonaktifkan.");
    }

    /**
     * Reset password paksa oleh admin — satu-satunya jalur reset karena
     * surat_users tidak punya email untuk self-service (arsitektur.md §7
     * poin 8). Password baru ditampilkan SEKALI lewat flash message.
     */
    public function resetPassword(SuratUser $user): RedirectResponse
    {
        $newPassword = Str::password(12);

        $user->forceFill([
            'password' => Hash::make($newPassword),
            'must_change_password' => true,
        ])->save();

        return back()->with('status', "Password '{$user->username}' direset. Password sementara: {$newPassword} (catat sekarang, tidak ditampilkan lagi).");
    }

    private function validateUser(Request $request, ?int $ignoreUserId = null): array
    {
        return $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('surat_users', 'username')->ignore($ignoreUserId)],
            'name' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::exists('roles', 'name')],
            'director_id' => ['nullable', 'exists:directors,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'nik' => ['nullable', 'string', 'max:20'],
        ]);
    }

    private function formOptions(): array
    {
        return [
            'roles' => Role::orderBy('name')->pluck('name'),
            'directors' => Director::orderBy('name')->get(['id', 'name', 'code']),
            'departments' => Department::orderBy('sort_order')->get(['id', 'code']),
        ];
    }
}
