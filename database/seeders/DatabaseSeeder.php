<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Data referensi & historis dimigrasi lewat command `legacy:migrate-*`
     * (lihat app/Console/Commands/Legacy/), bukan lewat seeder ini — kecuali
     * RBAC (roles/permissions), yang harus ada SEBELUM `legacy:migrate-references`
     * dijalankan (agar assignRole() punya role yang valid).
     *
     * Sengaja TIDAK memakai trait WithoutModelEvents — itu mematikan event
     * `saved`/`deleted` yang dipakai spatie/laravel-permission untuk auto-
     * invalidate cache permission, dan pernah menyebabkan
     * `PermissionDoesNotExist` palsu saat seeding (permission sudah ada di
     * DB tapi cache belum di-refresh).
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
    }
}
