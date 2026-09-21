<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan `suratbag.diskabag1..16` (disposisi Kabag) DAN
     * `suratbag.dis1..16` (instruksi Direksi di alur surat bagian) — model
     * ketiga yang terpisah dari `surat` di legacy (lihat arsitektur.md §5.4).
     * Reuse master `disposition_types` yang sama (DRY) — dikonfirmasi label
     * `diskabag1..16` IDENTIK dengan `dis1..16` di `inputsuratbag.asp`.
     * `actor_role` membedakan apakah baris ini dicatat Kabag (diskabag*) atau
     * Direksi (dis*), menggantikan pemisahan implisit nama kolom di legacy.
     *
     * Target direktur tambahan (`suratbag.dir1..8`) dipisah ke pivot
     * `letter_division_director_recipients` — sama alasannya dengan `D1..D41`
     * di tabel `letters`.
     */
    public function up(): void
    {
        Schema::create('letter_division_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_division_id')->constrained('letter_divisions')->cascadeOnDelete();
            $table->foreignId('disposition_type_id')->constrained('disposition_types');
            $table->enum('actor_role', ['department-head', 'director-secretary']);
            $table->foreignId('created_by')->constrained('surat_users');
            $table->dateTime('disposed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_division_dispositions');
    }
};
