<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot N:M menggantikan flag kolom `dir1..dir8` di tabel `suratbag`
     * legacy. Label dikonfirmasi via inputsuratbag.asp: dir1=DIRUT(kode 1),
     * dir2=DIRPROD(2), dir3=DIRKEU(3), dir4=DIRSDM(4), dir5=DIRRENBANG(5),
     * dir6=WADIRUT(11), dir7=DIRPEM(21), dir8=DIRKORP(7 — tidak ada di master
     * `directors`, lihat arsitektur.md §7 poin 2).
     */
    public function up(): void
    {
        Schema::create('letter_division_director_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_division_id')->constrained('letter_divisions')->cascadeOnDelete();
            $table->foreignId('director_id')->constrained('directors')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['letter_division_id', 'director_id'], 'division_director_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_division_director_recipients');
    }
};
