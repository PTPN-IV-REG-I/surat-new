<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Menggantikan flag `B1..B29` di tabel `surat` legacy. Diseed dari label
 * checkbox `inputsurat.asp` (tidak ada tabel master di legacy) — lihat
 * database/migrations/..._create_departments_table.php.
 */
class Department extends Model
{
    protected $fillable = [
        'legacy_flag',
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function letters(): BelongsToMany
    {
        return $this->belongsToMany(Letter::class, 'letter_recipients')->withTimestamps();
    }
}
