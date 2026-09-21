<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `letter_divisions` (Surat Bagian) juga butuh nomor agenda anti
     * race-condition, tapi punya ruang kode tipe sendiri (`agenda_type_code`)
     * yang terpisah dari `letters.letter_type`. Tanpa kolom domain, dua
     * domain yang kebetulan pakai kode sama (mis. keduanya punya tipe "I")
     * akan berbagi satu counter dan saling tabrakan nomor.
     */
    public function up(): void
    {
        Schema::table('agenda_counters', function (Blueprint $table) {
            $table->dropUnique(['letter_type', 'year']);
            $table->string('domain', 20)->default('letter')->after('id');
            $table->unique(['domain', 'letter_type', 'year']);
        });
    }

    public function down(): void
    {
        Schema::table('agenda_counters', function (Blueprint $table) {
            $table->dropUnique(['domain', 'letter_type', 'year']);
            $table->dropColumn('domain');
            $table->unique(['letter_type', 'year']);
        });
    }
};
