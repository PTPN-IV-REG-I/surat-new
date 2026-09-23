<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
        $baseQuery = $this->pendingFollowUpQuery($request);

        return view('reports.follow-up', [
            'total' => (clone $baseQuery)->count(),
            'overdueCount' => (clone $baseQuery)
                ->where('received_date', '<=', now()->subDays(7)->toDateString())
                ->count(),
        ]);
    }

    public function followUpData(Request $request)
    {
        return DataTables::eloquent($this->pendingFollowUpQuery($request)->with(['director', 'creator']))
            ->filter(function (Builder $query) use ($request) {
                $search = trim((string) $request->input('search.value'));

                if ($search === '') {
                    return;
                }

                $term = "%{$search}%";

                $query->where(function (Builder $query) use ($term) {
                    $query->where('letter_no', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('sender_name', 'like', $term);
                });
            })
            ->orderColumn('received_date', 'received_date $1, id $1')
            ->editColumn('received_date', fn (Letter $letter) => $letter->received_date?->format('d-m-Y') ?? '-')
            ->addColumn('waiting', function (Letter $letter) {
                $days = $letter->received_date?->diffInDays(now());

                if ($days === null) {
                    return '-';
                }

                $class = $days >= 7 ? 'bg-red-50 text-red-700' : 'bg-slate-100 text-slate-600';

                return '<span class="rounded-full px-2 py-0.5 text-xs font-medium '.$class.'">'.(int) $days.' hari</span>';
            })
            ->addColumn('director_name', fn (Letter $letter) => $letter->director?->name ?? '-')
            ->addColumn('creator_name', fn (Letter $letter) => $letter->creator?->name ?? '-')
            ->addColumn('action', fn (Letter $letter) => '<a href="'.e(route('letters.show', $letter)).'" class="text-emerald-600 hover:underline">Detail</a>')
            ->rawColumns(['waiting', 'action'])
            ->toJson();
    }

    private function pendingFollowUpQuery(Request $request): Builder
    {
        return Letter::query()
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()))
            ->where('follow_up', false);
    }
}
