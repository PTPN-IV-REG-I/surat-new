<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan tabel legacy `kodir` (13 baris terverifikasi di db_ptpn_3).
     *
     * `code` = kolom `kodir.kode` lama (mis. '1','11','12','2', dst — juga
     * dipakai sebagai suffix kolom D1/D11/D12/... di tabel `surat` legacy).
     * `legacy_label` menyimpan label asli `kodir.direksi` (era lama, mis.
     * "DIREKTUR UTAMA"/"SEVP PRODUKSI") karena label yang tampil di form aktif
     * (`inputsurat.asp`) sudah berbeda (mis. "DIRPEL"/"OPERATION HEAD I") —
     * bukti tiga era restrukturisasi organisasi berdampingan, lihat
     * arsitektur-surat-lama.md §5 temuan #6. `name` diisi label era terkini.
     *
     * Kode '7' (DIRKORP) sengaja diberi `is_active=false` secara default saat
     * seeding karena terverifikasi 0 baris `surat` pernah memakainya — lihat
     * arsitektur.md §7 poin 2.
     */
    public function up(): void
    {
        Schema::create('directors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('abbr')->nullable();
            $table->string('name');
            $table->string('legacy_label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directors');
    }
};
