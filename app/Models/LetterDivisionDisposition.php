<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Disposisi dalam alur Surat Bagian — `actor_role` membedakan Kabag
 * (`diskabag1..16`, aktor diketahui) vs Direksi (`dis1..16`, aktor tidak
 * tercatat di legacy). Reuse master `disposition_types` yang sama dengan
 * `LetterDisposition` (DRY) — lihat arsitektur.md §5.4.
 */
class LetterDivisionDisposition extends Model
{
    protected $fillable = [
        'letter_division_id',
        'disposition_type_id',
        'actor_role',
        'created_by',
        'disposed_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'disposed_at' => 'datetime',
        ];
    }

    public function letterDivision(): BelongsTo
    {
        return $this->belongsTo(LetterDivision::class);
    }

    public function dispositionType(): BelongsTo
    {
        return $this->belongsTo(DispositionType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(SuratUser::class, 'created_by');
    }
}
