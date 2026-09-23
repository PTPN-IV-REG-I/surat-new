<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Department;
use App\Models\Director;
use App\Models\SuratUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Kelola akun surat_users + role Spatie. Menggantikan fitur admin lama yang
 * hanya bisa lihat password plaintext (`passurat.asp`, sengaja TIDAK dibawa
 * — lihat arsitektur-surat-lama.md §3.1). Reset password di sini SELALU
 * generate ulang, tidak pernah menampilkan/menyimpan password lama.
 */
class UserController extends Controller
{
    /**
     * Tampilkan daftar akun pengguna dengan filter peran, status, per_page, dan pencarian.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $role = $request->input('role');
        $status = $request->input('status');

        $perPage = $request->integer('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $query = SuratUser::query()
            ->with(['roles', 'director', 'department']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $users = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $stats = SuratUser::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->first();

        $totalUsers = (int) ($stats->total ?? 0);
        $activeUsers = (int) ($stats->active ?? 0);
        $inactiveUsers = (int) ($stats->inactive ?? 0);
        $adminUsers = SuratUser::role('admin')->count();

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminUsers' => $adminUsers,
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return view('admin.users.form', $this->formOptions() + ['user' => null]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = SuratUser::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'director_id' => $data['director_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'nik' => $data['nik'] ?? null,
            'password' => Hash::make('12345678'),
            'is_active' => $request->boolean('is_active', true),
            'must_change_password' => true,
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.users.index')
            ->with('status', "Akun '{$user->username}' dibuat dengan password default: 12345678 (wajib diganti saat login pertama).");
    }

    public function edit(SuratUser $user)
    {
        $user->load('roles');

        return view('admin.users.form', $this->formOptions() + ['user' => $user]);
    }

    public function update(UpdateUserRequest $request, SuratUser $user): RedirectResponse
    {
        $data = $request->validated();

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
     * poin 8). Password baru default '12345678'.
     */
    public function resetPassword(SuratUser $user): RedirectResponse
    {
        $defaultPassword = '12345678';

        $user->forceFill([
            'password' => Hash::make($defaultPassword),
            'must_change_password' => false,
        ])->save();

        return back()->with('status', "Password '{$user->username}' berhasil direset ke: {$defaultPassword}");
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
