<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot N:M menggantikan flag kolom `D1,D11,D12,D2,D21,D22,D3,D31,D4,D41,
     * D5,D51,D7` di tabel `surat` legacy — direktur TAMBAHAN yang juga dituju
     * selain `letters.director_id` (kepada utama). Konsepnya sama seperti
     * `letter_recipients` (untuk departments), hanya untuk direktur.
     */
    public function up(): void
    {
        Schema::create('letter_director_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->foreignId('director_id')->constrained('directors')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['letter_id', 'director_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_director_recipients');
    }
};
