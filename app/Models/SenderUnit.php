<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menggantikan `t_kebun` (94 baris). BUKAN penentu tenant/scope kepemilikan
 * surat — murni master label pengirim untuk field `dari`, dan faktanya hanya
 * cocok untuk ~5% baris `letters` (sisanya pengirim eksternal, teks bebas di
 * `letters.sender_name`). Lihat arsitektur.md §6.6 & §14.
 */
class SenderUnit extends Model
{
    protected $fillable = [
        'code',
        'abbr',
        'name',
        'group_1',
        'group_2',
        'group_3',
        'level',
    ];

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }
}
