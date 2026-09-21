<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Laporan sederhana — item terakhir Fase 3 (arsitektur.md §11). Evaluasi
 * tindak lanjut: surat yang belum ditindaklanjuti (`follow_up=false`),
 * diurutkan dari yang paling lama menunggu (mengganti rekap manual yang
 * dulu hanya terlihat lewat highlight merah di lihat1.asp).
 */
class ReportController extends Controller
{
    use ScopesLetterVisibility;

    public function followUp(Request $request)
    {
        $baseQuery = Letter::query()
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->where('follow_up', false);

        $overdueCount = (clone $baseQuery)
            ->where('received_date', '<=', now()->subDays(7)->toDateString())
            ->count();

        $letters = (clone $baseQuery)
            ->with(['director', 'senderUnit', 'creator'])
            ->orderBy('received_date')
            ->paginate(20)
            ->withQueryString();

        return view('reports.follow-up', [
            'letters' => $letters,
            'overdueCount' => $overdueCount,
        ]);
    }
}
