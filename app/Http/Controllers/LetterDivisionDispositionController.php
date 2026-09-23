<?php

namespace App\Http\Controllers;

use App\Models\LetterDivision;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Instruksi disposisi Surat Bagian (dulu `diskabag1..16` + `dis1..16` di
 * inputsuratbag.asp). Route ini digerbangi `can:letter-divisions.manage`
 * (lihat routes/web.php), jadi hanya Kepala Bagian/admin yang bisa
 * mencapainya -- `actor_role` karena itu selalu 'department-head'.
 *
 * Disposisi Direksi (`dis1..16` legacy, actor_role 'director-secretary')
 * SENGAJA belum diimplementasikan di sini: skema permission saat ini tidak
 * memberi director-secretary akses baca ke letter_divisions sama sekali
 * (tidak ada permission "view" terpisah dari "manage"), jadi menambahkan
 * jalur dispose untuk mereka tanpa jalur lihat lebih dulu tidak berguna.
 * Lihat LetterDivisionDisposition model & arsitektur.md §5.4.
 */
class LetterDivisionDispositionController extends Controller
{
    public function store(Request $request, LetterDivision $division): RedirectResponse
    {
        $data = $request->validate([
            'disposition_type_ids' => ['required', 'array', 'min:1'],
            'disposition_type_ids.*' => ['exists:disposition_types,id'],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($division, $data, $request) {
            $now = now();
            $userId = $request->user()->id;
            foreach ($data['disposition_type_ids'] as $dispositionTypeId) {
                $division->dispositions()->create([
                    'disposition_type_id' => $dispositionTypeId,
                    'actor_role' => 'department-head',
                    'created_by' => $userId,
                    'disposed_at' => $now,
                    'note' => $data['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('letter-divisions.show', $division)->with('status', 'Disposisi ditambahkan.');
    }
}
