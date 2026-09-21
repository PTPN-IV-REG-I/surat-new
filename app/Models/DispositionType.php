<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master jenis instruksi disposisi. 18 baris aktif (`dis_new1..18`, label
 * final yang dipakai UI baru) + 16 baris nonaktif (`legacy_dis1..16`, label
 * asli `dis1..16`/`diskabag1..16` legacy — dipertahankan untuk integritas
 * data historis, TIDAK ditampilkan sebagai pilihan baru). Lihat
 * arsitektur.md §5.2 & §14.
 */
class DispositionType extends Model
{
    protected $fillable = [
        'code',
        'label',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function letterDispositions(): HasMany
    {
        return $this->hasMany(LetterDisposition::class);
    }

    public function letterDivisionDispositions(): HasMany
    {
        return $this->hasMany(LetterDivisionDisposition::class);
    }
}
