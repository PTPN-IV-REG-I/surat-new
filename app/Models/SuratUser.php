<?php

namespace App\Models;

use Database\Factories\SuratUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Menggantikan tabel legacy `login`. Login berbasis username (bukan email) —
 * lihat surat-new/readme/arsitektur.md §6.3 & §6.6.
 *
 * Peran (admin, garden-officer, department-head, director-secretary)
 * SEPENUHNYA dikelola lewat spatie/laravel-permission (`hasRole()`,
 * `assignRole()`, dst) — tidak ada kolom `role` string di tabel ini, supaya
 * tidak ada dua sumber kebenaran peran. `director_id` tetap kolom asli
 * karena itu scope data (direktur mana), bukan peran itu sendiri. Akun
 * sistem `legacy-migration` (lihat §14) sengaja TIDAK diberi role apapun dan
 * `is_active=false` — tidak untuk login.
 */
class SuratUser extends Authenticatable
{
    /** @use HasFactory<SuratUserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'username',
        'director_id',
        'department_id',
        'nik',
        'name',
        'password',
        'is_active',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Direktur yang dilayani — hanya relevan untuk role director-secretary.
     */
    public function director(): BelongsTo
    {
        return $this->belongsTo(Director::class);
    }

    /**
     * Bagian yang dipimpin — hanya relevan untuk role department-head. Tidak
     * bisa diresolve otomatis dari data legacy, lihat migration.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class, 'created_by');
    }

    public function letterDispositions(): HasMany
    {
        return $this->hasMany(LetterDisposition::class, 'created_by');
    }

    public function letterDivisions(): HasMany
    {
        return $this->hasMany(LetterDivision::class, 'created_by');
    }
}
