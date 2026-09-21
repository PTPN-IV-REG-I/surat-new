<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * RBAC awal — daftar permission & pemetaan role sesuai
 * surat-new/readme/arsitektur.md §10. Satu guard saja ('web'), berbeda dari
 * portal-new yang punya dua model auth.
 */
class RolePermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'letters.view-own-garden',
        'letters.view-department',
        'letters.view-director',
        'letters.view-all',
        'letters.create',
        'letters.update',
        'letters.delete',
        'letters.dispose',
        'letter-divisions.manage',
        'references.manage',
        'admin.users',
        'admin.roles',
    ];

    private const ROLE_PERMISSIONS = [
        'admin' => self::PERMISSIONS, // akses penuh

        'garden-officer' => [
            'letters.view-own-garden',
            'letters.create',
            'letters.update',
            'letters.dispose',
        ],

        'department-head' => [
            'letters.view-department',
            'letter-divisions.manage',
        ],

        'director-secretary' => [
            'letters.view-director',
            'letters.dispose',
        ],
    ];

    public function run(): void
    {
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Role::query()->delete();
        Permission::query()->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
        }

        // Wajib di-flush lagi di sini: DatabaseSeeder memakai WithoutModelEvents,
        // jadi event `saved` yang biasanya auto-invalidate cache Spatie tidak
        // pernah terpicu saat loop create() di atas — tanpa baris ini,
        // syncPermissions() di bawah akan gagal "permission does not exist"
        // padahal baris di DB sudah ada.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::ROLE_PERMISSIONS as $roleName => $permissions) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
