<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Department;
use App\Models\Director;
use App\Models\DispositionType;
use App\Models\Letter;
use App\Models\SenderUnit;
use App\Services\AgendaNumberService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Arsip + input/edit surat (Kebun) — menggantikan `lihat1.asp`/`search.asp`
 * (baca) dan `inputsurat.asp`/`editsurat.asp` (tulis). Instruksi disposisi
 * SENGAJA tidak ada di form ini — itu alur terpisah untuk Sekretaris
 * Direksi/Kepala Bagian, lihat LetterDispositionController (arsitektur.md
 * §11 Fase 2).
 */
class LetterController extends Controller
{
    use ScopesLetterVisibility;

    public function index(Request $request)
    {
        $user = $request->user();

        $baseQuery = Letter::query();
        $this->applyScope($baseQuery, $user);

        $stats = (clone $baseQuery)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN follow_up = 1 THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN follow_up = 0 OR follow_up IS NULL THEN 1 ELSE 0 END) as in_progress
            ')
            ->first();

        $totalCount = (int) ($stats->total ?? 0);
        $completedCount = (int) ($stats->completed ?? 0);
        $inProgressFollowUpCount = (int) ($stats->in_progress ?? 0);
        $pendingDispositionsCount = (clone $baseQuery)->doesntHave('dispositions')->count();

        $query = (clone $baseQuery)->with([
            'director',
            'senderUnit',
            'departmentRecipients',
            'directorRecipients',
            'dispositions.dispositionType',
        ]);
        $this->applyDateFilter($query, $request);

        if ($request->filled('type')) {
            $query->where('letter_type', $request->input('type'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'pending_disposition') {
                $query->doesntHave('dispositions');
            } elseif ($request->input('status') === 'disposed') {
                $query->has('dispositions');
            } elseif ($request->input('status') === 'need_followup') {
                $query->where('follow_up', false);
            } elseif ($request->input('status') === 'completed') {
                $query->where('follow_up', true);
            }
        }

        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('letter_no', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhere('sender_name', 'like', $term)
                    ->orWhere('keyword', 'like', $term)
                    ->orWhereHas('senderUnit', fn (Builder $sq) => $sq->where('name', 'like', $term)->orWhere('abbr', 'like', $term))
                    ->orWhereHas('director', fn (Builder $dq) => $dq->where('name', 'like', $term));
            });
        }

        $perPage = in_array($request->integer('per_page'), [10, 25, 50, 100], true) ? $request->integer('per_page') : 10;

        $letters = $query
            ->orderByDesc('received_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('letters.index', compact(
            'letters',
            'totalCount',
            'pendingDispositionsCount',
            'inProgressFollowUpCount',
            'completedCount'
        ));
    }

    public function export(Request $request)
    {
        $user = $request->user();

        $query = Letter::query()->with(['director', 'senderUnit']);
        $this->applyScope($query, $user);

        $this->applyDateFilter($query, $request);
        if ($request->filled('type')) {
            $query->where('letter_type', $request->input('type'));
        }
        if ($request->filled('search')) {
            $term = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('letter_no', 'like', $term)
                    ->orWhere('subject', 'like', $term)
                    ->orWhere('sender_name', 'like', $term);
            });
        }

        $letters = $query->orderByDesc('received_date')->orderByDesc('id')->take(2000)->get();

        $filename = 'arsip-surat-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($letters) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['No Agenda', 'No Surat', 'Tanggal Surat', 'Tanggal Terima', 'Perihal', 'Pengirim', 'Tujuan Direktur', 'Tindak Lanjut']);

            foreach ($letters as $letter) {
                fputcsv($handle, [
                    "{$letter->letter_type}-{$letter->agenda_no}",
                    $letter->letter_no,
                    $letter->letter_date?->format('d/m/Y') ?? '-',
                    $letter->received_date?->format('d/m/Y') ?? '-',
                    $letter->subject,
                    $letter->senderUnit?->name ?? $letter->sender_name ?? '-',
                    $letter->director?->name ?? '-',
                    $letter->follow_up ? 'Selesai' : 'Belum Selesai',
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Endpoint server-side DataTables untuk arsip — 30 ribu+ baris, jadi
     * tidak bisa dimuat sekaligus ke client. Pencarian global sengaja
     * custom (bukan bawaan yajra) supaya bisa menjangkau kolom relasi
     * (pengirim/direktur) yang tidak ada di tabel `letters` sendiri.
     */
    public function data(Request $request)
    {
        $query = Letter::query()
            ->with(['director', 'senderUnit'])
            ->tap(fn (Builder $query) => $this->applyScope($query, $request->user()));

        $this->applyDateFilter($query, $request);

        return DataTables::eloquent($query)
            ->filter(function (Builder $query) use ($request) {
                $search = trim((string) $request->input('search.value'));

                if ($search === '') {
                    return;
                }

                $term = "%{$search}%";

                $query->where(function (Builder $query) use ($term) {
                    $query->where('letter_no', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('sender_name', 'like', $term)
                        ->orWhere('keyword', 'like', $term)
                        ->orWhereHas('senderUnit', fn (Builder $q) => $q->where('name', 'like', $term)->orWhere('abbr', 'like', $term))
                        ->orWhereHas('director', fn (Builder $q) => $q->where('name', 'like', $term));
                });
            })
            ->orderColumn('received_date', 'received_date $1, id $1')
            ->orderColumn('letter_date', 'letter_date $1, id $1')
            ->orderColumn('agenda', 'letter_type $1, CAST(agenda_no AS UNSIGNED) $1')
            ->addColumn('agenda', fn (Letter $letter) => "{$letter->letter_type}-{$letter->agenda_no}{$letter->agenda_series}")
            ->editColumn('letter_date', fn (Letter $letter) => $letter->letter_date?->format('d-m-Y') ?? '-')
            ->editColumn('received_date', fn (Letter $letter) => $letter->received_date?->format('d-m-Y') ?? '-')
            ->addColumn('director_name', fn (Letter $letter) => $letter->director?->name ?? '-')
            ->addColumn('sender', fn (Letter $letter) => $letter->senderUnit?->name ?? $letter->sender_name ?? '-')
            ->addColumn('status', fn (Letter $letter) => view('letters._status-badge', ['letter' => $letter])->render())
            ->addColumn('action', fn (Letter $letter) => '<a href="'.e(route('letters.show', $letter)).'" class="text-emerald-600 hover:underline">Detail</a>')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function show(Request $request, Letter $letter)
    {
        $this->applyScope(Letter::query()->whereKey($letter->id), $request->user())->firstOrFail();

        $letter->load([
            'director',
            'senderUnit',
            'creator',
            'directorRecipients',
            'departmentRecipients',
            'dispositions.dispositionType',
            'dispositions.creator',
        ]);

        return view('letters.show', [
            'letter' => $letter,
            'dispositionTypes' => DispositionType::active()->orderBy('sort_order')->get(),
        ]);
    }

    public function print(Request $request, Letter $letter)
    {
        $this->applyScope(Letter::query()->whereKey($letter->id), $request->user())->firstOrFail();

        $letter->load(['director', 'senderUnit', 'directorRecipients', 'departmentRecipients', 'dispositions.dispositionType']);

        return view('letters.print', [
            'letter' => $letter,
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('letters.form', $this->formOptions() + ['letter' => null]);
    }

    public function store(StoreLetterRequest $request, AgendaNumberService $agendaNumbers): RedirectResponse
    {
        $data = $request->validated();

        $receivedDate = $data['received_date'] ?? $data['letter_date'] ?? now()->toDateString();
        $agendaNo = $agendaNumbers->next($data['letter_type'], (int) date('Y', strtotime($receivedDate)));

        $letter = Letter::create([
            ...$this->mappedFields($data),
            'letter_type' => $data['letter_type'],
            'agenda_no' => (string) $agendaNo,
            'attachment_path' => $this->storeAttachment($request),
            'follow_up' => false,
            'created_by' => $request->user()->id,
        ]);

        $letter->directorRecipients()->sync($data['director_recipients'] ?? []);
        $letter->departmentRecipients()->sync($data['department_recipients'] ?? []);

        return redirect()->route('letters.show', $letter)
            ->with('status', "Surat disimpan dengan No. Agenda {$data['letter_type']}-{$agendaNo}{$letter->agenda_series}.");
    }

    public function edit(Request $request, Letter $letter)
    {
        $this->authorizeUpdate($request, $letter);

        $letter->load(['directorRecipients', 'departmentRecipients']);

        return view('letters.form', $this->formOptions() + ['letter' => $letter]);
    }

    public function update(UpdateLetterRequest $request, Letter $letter): RedirectResponse
    {
        $this->authorizeUpdate($request, $letter);

        $data = $request->validated();

        $letter->update([
            ...$this->mappedFields($data),
            'follow_up' => $request->boolean('follow_up'),
            'attachment_path' => $this->storeAttachment($request) ?? $letter->attachment_path,
        ]);

        $letter->directorRecipients()->sync($data['director_recipients'] ?? []);
        $letter->departmentRecipients()->sync($data['department_recipients'] ?? []);

        return redirect()->route('letters.show', $letter)->with('status', 'Surat diperbarui.');
    }

    public function destroy(Request $request, Letter $letter): RedirectResponse
    {
        $this->authorizeUpdate($request, $letter);

        $letter->delete();

        return redirect()->route('letters.index')->with('status', "Surat {$letter->letter_no} dihapus.");
    }

    private function authorizeUpdate(Request $request, Letter $letter): void
    {
        $user = $request->user();

        abort_unless($user->can('letters.update') && ($user->can('letters.view-all') || $letter->created_by === $user->id), 403);
    }

    private function mappedFields(array $data): array
    {
        return [
            'director_id' => $data['director_id'] ?? null,
            'agenda_series' => $data['agenda_series'] ?? null,
            'subject' => $data['subject'],
            'content' => $data['content'] ?? null,
            'letter_date' => $data['letter_date'] ?? null,
            'received_date' => $data['received_date'] ?? null,
            'pages' => $data['pages'] ?? null,
            'location' => $data['location'] ?? null,
            'sender_unit_id' => $data['sender_unit_id'] ?? null,
            'sender_name' => $data['sender_name'] ?? null,
            'keyword' => $data['keyword'] ?? null,
            'letter_no' => $data['letter_no'],
        ];
    }

    private function storeAttachment(Request $request): ?string
    {
        if (! $request->hasFile('attachment')) {
            return null;
        }

        return $request->file('attachment')->store('letters', 'public');
    }

    private function applyDateFilter(Builder $query, Request $request): void
    {
        if ($request->filled('date')) {
            $dateVal = trim((string) $request->input('date'));
            $dates = preg_split('/\s+(?:to|-)\s+/', $dateVal);

            if (count($dates) >= 2) {
                $start = trim($dates[0]);
                $end = trim($dates[1]);
                if ($start > $end) {
                    [$start, $end] = [$end, $start];
                }
                $query->where(function (Builder $q) use ($start, $end) {
                    $q->whereBetween('received_date', [$start, $end])
                      ->orWhereBetween('letter_date', [$start, $end]);
                });
            } else {
                $single = trim($dates[0]);
                $query->where(function (Builder $q) use ($single) {
                    $q->whereDate('received_date', $single)
                      ->orWhereDate('letter_date', $single);
                });
            }
        } else {
            if ($request->filled('month')) {
                $query->whereMonth('received_date', $request->integer('month'));
            }
            if ($request->filled('year')) {
                $query->whereYear('received_date', $request->integer('year'));
            }
        }
    }

    private function formOptions(): array
    {
        return [
            'directors' => Director::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'abbr']),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(['id', 'code', 'name']),
            'senderUnits' => SenderUnit::orderBy('name')->get(['id', 'name', 'abbr']),
        ];
    }

}
