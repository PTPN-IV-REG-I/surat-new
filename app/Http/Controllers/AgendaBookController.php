<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Buku Agenda: register surat diurutkan per No. Agenda (bukan tanggal
 * terima seperti Arsip Surat) dan dikelompokkan per jenis. `buku.asp` yang
 * dulu jadi backend AJAX halaman `agenda.asp` sudah tidak ada di aplikasi
 * lama (file hilang), jadi tidak ada perilaku persis untuk direplikasi —
 * lihat catatan investigasi di riwayat percakapan. Melengkapi item
 * terakhir Fase 2 (arsitektur.md §11).
 */
class AgendaBookController extends Controller
{
    use ScopesLetterVisibility;

    public function index(Request $request)
    {
        $year = $request->integer('year') ?: now()->year;

        $letters = Letter::query()
            ->with(['director', 'senderUnit'])
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->whereYear('received_date', $year)
            ->when($request->filled('letter_type'), fn (Builder $query) => $query->where('letter_type', $request->string('letter_type')))
            ->orderBy('letter_type')
            ->orderByRaw('CAST(agenda_no AS UNSIGNED)')
            ->get()
            ->groupBy('letter_type');

        return view('letters.agenda-book', [
            'letters' => $letters,
            'year' => $year,
            'letterType' => $request->string('letter_type'),
        ]);
    }
}
