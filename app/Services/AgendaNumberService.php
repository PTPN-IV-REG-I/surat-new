<?php

namespace App\Services;

use App\Models\AgendaCounter;
use App\Models\Letter;
use App\Models\LetterDivision;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Generate nomor agenda berikutnya dengan lock baris — menggantikan
 * `showlastnum()`/`agen()` lama yang menghitung `MAX(noagenda)+1` di
 * client-side tanpa proteksi konkurensi (arsitektur.md §11 Fase 2). Lihat
 * migration `create_agenda_counters_table` untuk rasional skema.
 *
 * Dua domain terpisah (`letter`/`division`) karena `letters.letter_type`
 * dan `letter_divisions.agenda_type_code` adalah ruang kode independen —
 * tanpa pemisahan ini, dua domain yang kebetulan pakai kode sama akan
 * berbagi satu counter dan saling tabrakan nomor.
 */
class AgendaNumberService
{
    public function next(string $letterType, int $year): int
    {
        return $this->nextForDomain('letter', $letterType, $year, fn () => (int) Letter::query()
            ->where('letter_type', $letterType)
            ->whereYear('received_date', $year)
            ->max(DB::raw('CAST(agenda_no AS UNSIGNED)')));
    }

    public function nextForDivision(string $typeCode, int $year): int
    {
        return $this->nextForDomain('division', $typeCode, $year, fn () => (int) LetterDivision::query()
            ->where('agenda_type_code', $typeCode)
            ->whereYear('agenda_date', $year)
            ->max('agenda_no'));
    }

    private function nextForDomain(string $domain, string $code, int $year, \Closure $seedFromExisting): int
    {
        return DB::transaction(function () use ($domain, $code, $year, $seedFromExisting) {
            $counter = AgendaCounter::query()
                ->where('domain', $domain)
                ->where('letter_type', $code)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = $this->seedCounter($domain, $code, $year, $seedFromExisting);
            }

            $counter->increment('last_number');

            return $counter->last_number;
        });
    }

    /**
     * Seed dari MAX(agenda_no) data existing (termasuk historis hasil
     * migrasi) supaya nomor baru tidak tabrakan dengan nomor tahun berjalan
     * yang sudah dipakai sebelum fitur ini ada. Dibungkus try/catch karena
     * dua request bersamaan bisa sama-sama tidak menemukan counter dan
     * sama-sama mencoba insert pertama — constraint unique di MySQL tidak
     * mem-batalkan transaksi, jadi cukup re-fetch dengan lock begitu insert
     * kedua gagal.
     */
    private function seedCounter(string $domain, string $code, int $year, \Closure $seedFromExisting): AgendaCounter
    {
        try {
            return AgendaCounter::create([
                'domain' => $domain,
                'letter_type' => $code,
                'year' => $year,
                'last_number' => $seedFromExisting(),
            ]);
        } catch (QueryException $e) {
            $counter = AgendaCounter::query()
                ->where('domain', $domain)
                ->where('letter_type', $code)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                throw $e;
            }

            return $counter;
        }
    }
}
