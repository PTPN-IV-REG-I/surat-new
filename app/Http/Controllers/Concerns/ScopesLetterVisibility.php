<?php

namespace App\Http\Controllers\Concerns;

use App\Models\SuratUser;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scoping akses baca surat per permission (letters.view-*), dipakai
 * LetterController (arsip) dan LetterDispositionController (disposisi harus
 * bisa lihat suratnya dulu). Lihat arsitektur.md §10.
 */
trait ScopesLetterVisibility
{
    private function applyScope(Builder $query, SuratUser $user): Builder
    {
        if ($user->can('letters.view-all')) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user) {
            // Kondisi mustahil sebagai basis: setiap izin di bawah HANYA
            // memperluas (orWhere), tidak pernah mempersempit. Tanpa baris
            // ini, user tanpa izin view apapun akan melihat SEMUA surat
            // (closure kosong = tidak ada filter sama sekali di Laravel).
            $query->whereRaw('1 = 0');

            if ($user->can('letters.view-own-garden')) {
                $query->orWhere('created_by', $user->id);
            }

            if ($user->can('letters.view-director') && $user->director_id) {
                $query->orWhere('director_id', $user->director_id)
                    ->orWhereHas('directorRecipients', fn (Builder $q) => $q->whereKey($user->director_id));
            }

            if ($user->can('letters.view-department') && $user->department_id) {
                $query->orWhereHas('departmentRecipients', fn (Builder $q) => $q->whereKey($user->department_id));
            }
        });
    }
}
