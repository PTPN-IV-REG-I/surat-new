<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menggantikan tabel legacy `surat` (33.978 baris terverifikasi di
     * db_ptpn_3). Kolom disposisi (`dis1..16`, `dis_new1..19`, `D1..D41`,
     * `B1..B29`) DIHILANGKAN dari sini — digantikan tabel relasi
     * `letter_dispositions` & `letter_recipients` (lihat migration
     * berikutnya). `kebun` (legacy) menjadi `created_by` (FK surat_users),
     * BUKAN `sender_unit_id` — lihat arsitektur.md §6.6 untuk alasan.
     *
     * Tidak ada unique constraint pada `letter_no`: legacy mengecek keunikan
     * nomor surat secara GLOBAL (checknosur.asp) tapi ini belum dikonfirmasi
     * sebagai aturan bisnis yang benar (lihat arsitektur-surat-lama.md §3.7)
     * — indeks biasa dulu, unique constraint menyusul setelah dikonfirmasi
     * & data historis direkonsiliasi (Fase 4).
     */
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();

            // identitas surat (kunci komposit legacy: jns+noagenda+seri+nomor+tahun)
            $table->string('letter_type', 10); // jns
            $table->string('agenda_no', 20);
            $table->string('agenda_series', 10)->nullable(); // seri
            $table->string('letter_no', 50)->index(); // nomor

            $table->foreignId('director_id')->nullable()
                ->constrained('directors')->nullOnDelete(); // kepada (tujuan utama)

            $table->text('subject'); // hal
            $table->text('content')->nullable(); // isi

            $table->date('letter_date')->nullable(); // tglsurat
            $table->date('received_date')->nullable()->index(); // tglterima

            $table->unsignedInteger('pages')->nullable(); // lbr
            $table->string('location', 50)->nullable(); // lokasi

            // pengirim: dropdown master (sender_units) ATAU teks bebas ("Lain-lain")
            $table->foreignId('sender_unit_id')->nullable()
                ->constrained('sender_units')->nullOnDelete();
            $table->string('sender_name')->nullable(); // dari (fallback teks bebas)

            $table->string('keyword')->nullable(); // katakunci
            $table->string('attachment_path')->nullable(); // gambar

            $table->boolean('follow_up')->default(false); // tl
            $table->string('status', 20)->nullable(); // T

            $table->date('date_f')->nullable(); // tglF (evaluasi tindak lanjut)
            $table->date('date_kf')->nullable(); // tglKF
            $table->date('date_tsd')->nullable(); // tglTSD

            $table->foreignId('created_by')->constrained('surat_users'); // pengganti `kebun`

            $table->timestamps();
            $table->softDeletes();

            $table->index(['letter_type', 'agenda_no', 'agenda_series']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
