<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use App\Models\LetterDisposition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Instruksi disposisi (dulu checkbox `dis_new1..18` di inputsurat.asp) —
 * dipisah dari input surat karena secara alur ini diisi belakangan oleh
 * Sekretaris Direksi (atau Kebun sendiri untuk suratnya sendiri), bukan
 * saat surat pertama kali dicatat. Satu submit bisa membuat beberapa baris
 * `letter_dispositions` sekaligus (dulu banyak flag boolean dicentang
 * bersamaan). Lihat arsitektur.md §5.2 & §11 Fase 2.
 */
class LetterDispositionController extends Controller
{
    use ScopesLetterVisibility;

    public function store(Request $request, Letter $letter): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('letters.dispose'), 403);
        $this->applyScope(Letter::query()->whereKey($letter->id), $user)->firstOrFail();

        $data = $request->validate([
            'disposition_type_ids' => ['required', 'array', 'min:1'],
            'disposition_type_ids.*' => ['exists:disposition_types,id'],
            'note' => ['nullable', 'string'],
        ]);

        foreach ($data['disposition_type_ids'] as $dispositionTypeId) {
            $letter->dispositions()->create([
                'disposition_type_id' => $dispositionTypeId,
                'created_by' => $user->id,
                'disposed_at' => now(),
                'note' => $data['note'] ?? null,
            ]);
        }

        return redirect()->route('letters.show', $letter)->with('status', 'Disposisi ditambahkan.');
    }

    public function destroy(Request $request, Letter $letter, LetterDisposition $disposition): RedirectResponse
    {
        $user = $request->user();

        abort_unless($disposition->letter_id === $letter->id, 404);
        abort_unless($user->can('letters.dispose') && ($user->can('letters.view-all') || $disposition->created_by === $user->id), 403);

        $disposition->delete();

        return redirect()->route('letters.show', $letter)->with('status', 'Disposisi dihapus.');
    }
}
