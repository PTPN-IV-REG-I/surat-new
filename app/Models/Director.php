<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menggantikan tabel legacy `kodir` (13 baris). `name` = label era terkini
 * (dari checkbox aktif inputsurat.asp), `legacy_label` = label asli kodir.
 * Lihat arsitektur-surat-lama.md §5 temuan #6 (tiga era restrukturisasi
 * organisasi berdampingan).
 */
class Director extends Model
{
    protected $fillable = [
        'code',
        'abbr',
        'name',
        'legacy_label',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Surat yang kepada utamanya (`letters.director_id`) direktur ini. */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /** Surat yang men-cc direktur ini sebagai tujuan tambahan (D1..D41 dulu). */
    public function ccLetters(): BelongsToMany
    {
        return $this->belongsToMany(Letter::class, 'letter_director_recipients')->withTimestamps();
    }

    public function divisionCcLetters(): BelongsToMany
    {
        return $this->belongsToMany(LetterDivision::class, 'letter_division_director_recipients')->withTimestamps();
    }

    public function secretaries(): HasMany
    {
        return $this->hasMany(SuratUser::class);
    }
}
