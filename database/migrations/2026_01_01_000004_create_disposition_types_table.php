<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Master jenis instruksi disposisi — satu sumber kebenaran menggantikan
     * TIGA skema kolom legacy yang tidak sinkron: `surat.dis1..dis16` (label
     * ditulis Sekretaris Direksi), `surat.dis_new1..dis_new19` (ditulis
     * Kebun), dan `suratbag.diskabag1..16`/`dis1..16` (Kabag). Lihat
     * arsitektur.md §5 untuk keputusan unifikasi & arsitektur-surat-lama.md
     * §6.1 untuk perbedaan jumlah kolom aktual vs yang dipakai kode.
     *
     * Label final memakai skema `dis_new1..18` (README-DISPOSISI-NEW.md) —
     * paling baru dan aktif dipakai user saat ini. `dis_new19` TIDAK disertakan
     * sebagai baris default karena tidak dipakai/dirender file manapun yang
     * dianalisis; maknanya perlu diklarifikasi dulu sebelum ditambahkan (lihat
     * arsitektur.md §7 poin 3). Seed data ditulis terpisah (seeder), bukan di
     * migration ini.
     */
    public function up(): void
    {
        Schema::create('disposition_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposition_types');
    }
};
