<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan tabel legacy `suratbag` (19.045 baris terverifikasi,
     * terkonsentrasi 99.8% di satu akun/unit — lihat arsitektur.md §7 poin 7).
     *
     * `agenda_no`/`agenda_type_code` memecah kolom legacy `noagenda` yang
     * disimpan sebagai string gabungan "angka/kodeTipe" (mis. "08/SP-III"),
     * dulu diparsing pakai CHARINDEX/LEFT/RIGHT di checknobag.asp — lihat
     * arsitektur-surat-lama.md §5 temuan #9. `created_by` menggantikan kolom
     * `nama` legacy yang ternyata menyimpan USERNAME, bukan nama asli
     * (temuan #8) — sumber kebingungan yang sengaja tidak dibawa ke skema
     * baru.
     */
    public function up(): void
    {
        Schema::create('letter_divisions', function (Blueprint $table) {
            $table->id();

            $table->string('division_type', 10)->nullable(); // jenis
            $table->string('type_code', 10)->nullable(); // tipe

            $table->text('addressee')->nullable(); // kepada (teks bebas di suratbag)
            $table->string('sender_name')->nullable(); // dari

            $table->string('letter_no', 100)->nullable()->index(); // nosurat
            $table->date('letter_date')->nullable(); // tglsurat
            $table->date('received_date')->nullable(); // tglterima
            $table->decimal('amount', 20, 4)->nullable(); // jumlah

            $table->unsignedInteger('agenda_no')->nullable(); // bagian angka dari noagenda
            $table->string('agenda_type_code', 20)->nullable(); // bagian kode dari noagenda
            $table->date('agenda_date')->nullable(); // tglagenda

            $table->text('subject')->nullable(); // hal
            $table->string('department_head', 10)->nullable(); // kabag (kode)
            $table->string('status', 50)->nullable();
            $table->string('matter')->nullable(); // urusan (teks bebas — master `urusurat` legacy hanya berisi 8 baris untuk 1 bagian)
            $table->text('note')->nullable(); // catatan

            $table->foreignId('created_by')->constrained('surat_users');
            $table->dateTime('input_at')->nullable(); // tglinput
            $table->dateTime('edited_at')->nullable(); // tgledit

            $table->timestamps();
            $table->softDeletes();

            $table->index(['division_type', 'agenda_no', 'agenda_type_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_divisions');
    }
};
