<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Matrix role x permission sederhana — mengganti hardcoded otoritas legacy
 * (`MM_authorizedUsers` per halaman) dengan RBAC yang bisa diubah tanpa
 * deploy kode. Menambah/menghapus role dilakukan lewat seeder
 * (RolePermissionSeeder), bukan lewat UI ini — daftar role terbatas & sudah
 * dirancang eksplisit di arsitektur.md §10.
 */
class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('status', "Permission role '{$role->name}' diperbarui.");
    }
}
