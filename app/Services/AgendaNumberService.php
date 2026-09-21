<?php

namespace App\Services;

use App\Models\AgendaCounter;
use App\Models\Letter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Generate nomor agenda berikutnya per (letter_type, year) dengan lock baris
 * — menggantikan `showlastnum()` lama yang menghitung `MAX(noagenda)+1` di
 * client-side tanpa proteksi konkurensi (arsitektur.md §11 Fase 2). Lihat
 * migration `create_agenda_counters_table` untuk rasional skema.
 */
class AgendaNumberService
{
    public function next(string $letterType, int $year): int
    {
        return DB::transaction(function () use ($letterType, $year) {
            $counter = AgendaCounter::query()
                ->where('letter_type', $letterType)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = $this->seedCounter($letterType, $year);
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
    private function seedCounter(string $letterType, int $year): AgendaCounter
    {
        try {
            $seed = (int) Letter::query()
                ->where('letter_type', $letterType)
                ->whereYear('received_date', $year)
                ->max(DB::raw('CAST(agenda_no AS UNSIGNED)'));

            return AgendaCounter::create([
                'letter_type' => $letterType,
                'year' => $year,
                'last_number' => $seed,
            ]);
        } catch (QueryException $e) {
            $counter = AgendaCounter::query()
                ->where('letter_type', $letterType)
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
