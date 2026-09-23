<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\DispositionType;
use App\Models\LetterDivision;
use App\Services\AgendaNumberService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

/**
 * Surat Bagian — menggantikan `inputsuratbag.asp`/`editsuratbag.asp`.
 * Berbeda dari `letters` (dilihat per Direktur/Kebun), `letter_divisions`
 * dikelola sepenuhnya oleh pemegang permission `letter-divisions.manage`
 * (Kepala Bagian/admin) — tidak ada kolom scoping per bagian di skema
 * (lihat migration), jadi tidak ada scoping tambahan di sini seperti
 * `ScopesLetterVisibility`. Instruksi disposisi Kabag/Direksi ada di alur
 * terpisah, lihat LetterDivisionDispositionController. Fase 3 (arsitektur.md
 * §11).
 */
class LetterDivisionController extends Controller
{
    public function index()
    {
        return view('letter-divisions.index');
    }

    /** Endpoint server-side DataTables — 19 ribu+ baris, tidak bisa dimuat sekaligus. */
    public function data(Request $request)
    {
        return DataTables::eloquent(LetterDivision::query())
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
            ->orderColumn('agenda', 'CAST(agenda_no AS UNSIGNED) $1, agenda_type_code $1')
            ->addColumn('agenda', fn (LetterDivision $division) => "{$division->agenda_no}/{$division->agenda_type_code}")
            ->editColumn('letter_no', fn (LetterDivision $division) => $division->letter_no ?? '-')
            ->editColumn('received_date', fn (LetterDivision $division) => $division->received_date?->format('d-m-Y') ?? '-')
            ->editColumn('sender_name', fn (LetterDivision $division) => $division->sender_name ?? '-')
            ->editColumn('status', fn (LetterDivision $division) => $division->status
                ? '<span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">'.e($division->status).'</span>'
                : '')
            ->addColumn('action', fn (LetterDivision $division) => '<a href="'.e(route('letter-divisions.show', $division)).'" class="text-emerald-600 hover:underline">Detail</a>')
            ->rawColumns(['status', 'action'])
            ->toJson();
    }

    public function show(LetterDivision $division)
    {
        $division->load(['creator', 'directorRecipients', 'dispositions.dispositionType', 'dispositions.creator']);

        return view('letter-divisions.show', [
            'division' => $division,
            'dispositionTypes' => DispositionType::active()->orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('letter-divisions.form', $this->formOptions() + ['division' => null]);
    }

    public function store(Request $request, AgendaNumberService $agendaNumbers): RedirectResponse
    {
        $data = $this->validateDivision($request);

        $agendaDate = $data['agenda_date'] ?? $data['received_date'] ?? now()->toDateString();
        $agendaNo = $agendaNumbers->nextForDivision($data['agenda_type_code'], (int) date('Y', strtotime($agendaDate)));

        $division = LetterDivision::create([
            ...$this->mappedFields($data),
            'agenda_no' => $agendaNo,
            'created_by' => $request->user()->id,
            'input_at' => now(),
        ]);

        $division->directorRecipients()->sync($data['director_recipients'] ?? []);

        return redirect()->route('letter-divisions.show', $division)
            ->with('status', "Surat bagian disimpan dengan No. Agenda {$agendaNo}/{$data['agenda_type_code']}.");
    }

    public function edit(LetterDivision $division)
    {
        $division->load('directorRecipients');

        return view('letter-divisions.form', $this->formOptions() + ['division' => $division]);
    }

    public function update(Request $request, LetterDivision $division): RedirectResponse
    {
        $data = $this->validateDivision($request);

        $division->update([
            ...$this->mappedFields($data),
            'edited_at' => now(),
        ]);

        $division->directorRecipients()->sync($data['director_recipients'] ?? []);

        return redirect()->route('letter-divisions.show', $division)->with('status', 'Surat bagian diperbarui.');
    }

    public function destroy(LetterDivision $division): RedirectResponse
    {
        $division->delete();

        return redirect()->route('letter-divisions.index')->with('status', 'Surat bagian dihapus.');
    }

    private function mappedFields(array $data): array
    {
        return [
            'division_type' => $data['division_type'] ?? null,
            'agenda_type_code' => $data['agenda_type_code'],
            'agenda_date' => $data['agenda_date'] ?? null,
            'addressee' => $data['addressee'] ?? null,
            'sender_name' => $data['sender_name'] ?? null,
            'letter_no' => $data['letter_no'] ?? null,
            'letter_date' => $data['letter_date'] ?? null,
            'received_date' => $data['received_date'] ?? null,
            'amount' => $data['amount'] ?? null,
            'subject' => $data['subject'],
            'department_head' => $data['department_head'] ?? null,
            'status' => $data['status'] ?? null,
            'matter' => $data['matter'] ?? null,
            'note' => $data['note'] ?? null,
        ];
    }

    private function validateDivision(Request $request): array
    {
        $data = $request->validate([
            'division_type' => ['nullable', 'string', 'max:10'],
            'agenda_type_code' => ['required', 'string', 'max:20'],
            'agenda_date' => ['nullable', 'date'],
            'addressee' => ['nullable', 'string'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'letter_no' => ['nullable', 'string', 'max:100'],
            'letter_date' => ['nullable', 'date'],
            'received_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'subject' => ['required', 'string'],
            'department_head' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'in:Sangat Segera,Segera,Rahasia,Biasa'],
            'matter' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'director_recipients' => ['nullable', 'array'],
            'director_recipients.*' => ['exists:directors,id'],
        ]);

        $data['agenda_type_code'] = Str::upper($data['agenda_type_code']);

        return $data;
    }

    private function formOptions(): array
    {
        return [
            'directors' => Director::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'abbr']),
        ];
    }
}
