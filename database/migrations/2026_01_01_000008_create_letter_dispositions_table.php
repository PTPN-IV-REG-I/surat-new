<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Model disposisi terpadu — satu baris = satu instruksi yang dicatat oleh
     * satu aktor (Kebun ATAU Sekretaris Direksi, dibedakan lewat `created_by`).
     * Menggantikan `surat.dis1..dis16` + `surat.dis_new1..dis_new19`.
     *
     * Target direktur tambahan (`surat.D1..D41`) SENGAJA tidak dimodelkan di
     * sini — itu bukan "jenis instruksi" tapi "siapa lagi yang dituju" (mirip
     * B1..B29/departments), jadi dipisah ke tabel pivot `letter_director_recipients`.
     * Lihat arsitektur.md §5.2.
     */
    public function up(): void
    {
        Schema::create('letter_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('disposition_type_id')->constrained('disposition_types');
            $table->foreignId('created_by')->constrained('surat_users');
            $table->dateTime('disposed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_dispositions');
    }
};
