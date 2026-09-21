<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Menggantikan tabel legacy `surat` (33.978 baris termigrasi). Kolom flag
 * disposisi/tujuan (`dis1..16`, `dis_new1..19`, `D1..D41`, `B1..B29`) TIDAK
 * ada di sini — lihat relasi `dispositions()`, `directorRecipients()`,
 * `departmentRecipients()`. Lihat arsitektur.md §6 & §14.
 */
class Letter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'letter_type',
        'agenda_no',
        'agenda_series',
        'letter_no',
        'director_id',
        'subject',
        'content',
        'letter_date',
        'received_date',
        'pages',
        'location',
        'sender_unit_id',
        'sender_name',
        'keyword',
        'attachment_path',
        'follow_up',
        'status',
        'date_f',
        'date_kf',
        'date_tsd',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'received_date' => 'date',
            'date_f' => 'date',
            'date_kf' => 'date',
            'date_tsd' => 'date',
            'follow_up' => 'boolean',
        ];
    }

    /** Tujuan utama (dulu `surat.kepada`). */
    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }

    /** Label pengirim ber-master, nullable (dulu dropdown t_kebun di `dari`). */
    public function senderUnit(): BelongsTo
    {
        return $this->belongsTo(SenderUnit::class);
    }

    /** Pemilik/pembuat surat — dulu `surat.kebun` (username, BUKAN kode unit). */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(SuratUser::class, 'created_by');
    }

    /** Direktur tambahan yang di-cc — dulu flag `D1..D41`. */
    public function directorRecipients(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'letter_director_recipients')->withTimestamps();
    }

    /** Bagian/biro penerima — dulu flag `B1..B29`. */
    public function departmentRecipients(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'letter_recipients')->withTimestamps();
    }

    /** Instruksi disposisi — dulu `dis1..16` + `dis_new1..19`. */
    public function dispositions(): HasMany
    {
        return $this->hasMany(LetterDisposition::class);
    }
}
