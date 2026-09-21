<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris = satu instruksi dicatat satu aktor untuk satu surat. Lihat
 * arsitektur.md §5.2 untuk rasional unifikasi model disposisi.
 */
class LetterDisposition extends Model
{
    protected $fillable = [
        'letter_id',
        'disposition_type_id',
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

    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
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
