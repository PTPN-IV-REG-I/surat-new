<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scope tambahan setelah `directors`/`departments` ada (hindari circular
     * FK pada migration dasar surat_users). Menggantikan `login.otoritas`.
     *
     * Peran (role/permission) TIDAK disimpan sebagai kolom string di sini —
     * itu sepenuhnya tanggung jawab spatie/laravel-permission (tabel roles/
     * model_has_roles), supaya tidak ada dua sumber kebenaran peran yang bisa
     * saling tidak sinkron. `director_id`/`department_id` tetap kolom asli
     * karena itu SCOPE data (direktur/bagian mana), bukan peran itu sendiri —
     * role "director-secretary" saja tidak cukup untuk tahu direktur mana
     * yang dilayani, begitu juga "department-head" untuk bagian mana.
     *
     * Role legacy -> role Spatie (arsitektur.md §10, diseed RolePermissionSeeder):
     *   1              -> admin
     *   2,21-25        -> garden-officer   (scope: akun sendiri, BUKAN unit —
     *                                        lihat arsitektur.md §6.6)
     *   3              -> department-head  (scope: department_id — TIDAK bisa
     *                                        diresolve otomatis dari `login`
     *                                        legacy, kosong sampai diisi manual
     *                                        lewat admin)
     *   01-07          -> director-secretary (scope: director_id)
     *
     * `nik` = referensi longgar ke `employees.nik` di database `ptpn`
     * (portal-new), tanpa FK fisik lintas database — lihat arsitektur.md §6.5.
     */
    public function up(): void
    {
        Schema::table('surat_users', function (Blueprint $table) {
            $table->foreignId('director_id')->nullable()->after('username')
                ->constrained('directors')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('director_id')
                ->constrained('departments')->nullOnDelete();
            $table->string('nik', 20)->nullable()->after('department_id');
        });
    }

    public function down(): void
    {
        Schema::table('surat_users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('director_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn(['nik']);
        });
    }
};
