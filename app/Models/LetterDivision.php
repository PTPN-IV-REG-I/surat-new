<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Menggantikan tabel legacy `suratbag` (19.045 baris termigrasi) — domain
 * Surat Bagian, model ketiga yang terpisah dari `letters`. Lihat
 * arsitektur.md §5.4 & §14.
 */
class LetterDivision extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'division_type',
        'type_code',
        'addressee',
        'sender_name',
        'letter_no',
        'letter_date',
        'received_date',
        'amount',
        'agenda_no',
        'agenda_type_code',
        'agenda_date',
        'subject',
        'department_head',
        'status',
        'matter',
        'note',
        'created_by',
        'input_at',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'received_date' => 'date',
            'agenda_date' => 'date',
            'input_at' => 'datetime',
            'edited_at' => 'datetime',
            'amount' => 'decimal:4',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(SuratUser::class, 'created_by');
    }

    /** Direktur yang di-cc — dulu flag `dir1..dir8`. */
    public function directorRecipients(): BelongsToMany
    {
        return $this->belongsToMany(Director::class, 'letter_division_director_recipients')->withTimestamps();
    }

    /** Disposisi Kabag (`diskabag1..16`) + Direksi (`dis1..16`) di alur ini. */
    public function dispositions(): HasMany
    {
        return $this->hasMany(LetterDivisionDisposition::class);
    }
}
