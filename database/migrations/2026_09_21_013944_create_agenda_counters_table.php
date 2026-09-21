<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Basis nomor urut agenda per (letter_type, year), dikunci lewat
     * lockForUpdate() di AgendaNumberService — menggantikan logika lama
     * `MAX(CONVERT(INT, noagenda))` yang dihitung di client-side tanpa lock
     * (inputsurat.asp `showlastnum()`), rawan dua user input bersamaan dapat
     * nomor sama. Baris pertama per kombinasi diseed dari MAX(agenda_no)
     * data existing (termasuk hasil migrasi historis Fase 4) supaya tidak
     * tabrakan dengan nomor tahun berjalan yang sudah ada.
     */
    public function up(): void
    {
        Schema::create('agenda_counters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_type', 10);
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();

            $table->unique(['letter_type', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_counters');
    }
};
