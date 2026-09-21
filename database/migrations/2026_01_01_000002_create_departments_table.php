<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan flag kolom `B1`..`B29` di tabel `surat` legacy (bagian/biro
     * penerima surat). Tidak ada tabel master untuk ini di legacy — label
     * diambil langsung dari checkbox di surat/inputsurat.asp (baris 568-632).
     * `legacy_flag` menyimpan nama kolom asal (B1..B29) untuk keperluan
     * mapping saat migrasi data Fase 4, `code` adalah label singkat yang
     * tampil di form (mis. "3.00", "BSDM", "3.19/BAKT").
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_flag', 10)->unique();
            $table->string('code');
            $table->string('name')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
