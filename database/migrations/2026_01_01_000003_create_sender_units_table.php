<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan tabel legacy `t_kebun` (94 baris). PENTING: tabel ini
     * BUKAN penentu tenant/scope kepemilikan surat — itu peran `surat_users`
     * (lihat migration surat_users & letters). `t_kebun` terverifikasi hanya
     * dipakai sebagai master label pengirim (field `dari` pada form input),
     * berisi nama jabatan/unit seperti "KEPALA BAGIAN UMUM (BUMU)",
     * "DIREKTUR PRODUKSI DAN PENGEMBANGAN (DPP)" — lihat koreksi di
     * arsitektur.md §6.6 dan arsitektur-surat-lama.md §6.2.
     */
    public function up(): void
    {
        Schema::create('sender_units', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('abbr')->nullable();
            $table->string('name')->nullable();
            $table->string('group_1')->nullable();
            $table->string('group_2')->nullable();
            $table->string('group_3')->nullable();
            $table->string('level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sender_units');
    }
};
