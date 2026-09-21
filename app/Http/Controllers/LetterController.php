<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesLetterVisibility;
use App\Models\Department;
use App\Models\Director;
use App\Models\DispositionType;
use App\Models\Letter;
use App\Models\SenderUnit;
use App\Services\AgendaNumberService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $letters = Letter::query()
            ->with(['director', 'senderUnit', 'creator'])
            ->tap(fn (Builder $query) => $this->applyScope($query, $user))
            ->when($request->filled('q'), function (Builder $query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function (Builder $query) use ($term) {
                    $query->where('letter_no', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('sender_name', 'like', $term)
                        ->orWhere('keyword', 'like', $term);
                });
            })
            ->when($request->filled('month'), fn (Builder $query) => $query->whereMonth('received_date', $request->integer('month')))
            ->when($request->filled('year'), fn (Builder $query) => $query->whereYear('received_date', $request->integer('year')))
            ->orderByDesc('received_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('letters.index', [
            'letters' => $letters,
            'q' => $request->string('q'),
            'month' => $request->integer('month'),
            'year' => $request->integer('year'),
        ]);
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

    public function create()
    {
        return view('letters.form', $this->formOptions() + ['letter' => null]);
    }

    public function store(Request $request, AgendaNumberService $agendaNumbers): RedirectResponse
    {
        $data = $this->validateLetter($request, requireType: true);

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

    public function update(Request $request, Letter $letter): RedirectResponse
    {
        $this->authorizeUpdate($request, $letter);

        $data = $this->validateLetter($request, requireType: false);

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

    /**
     * `letter_type` hanya divalidasi saat create — form edit menguncinya
     * (read-only, tidak dikirim) karena mengubahnya berarti mengubah basis
     * penomoran agenda yang sudah terlanjur dipakai.
     */
    private function validateLetter(Request $request, bool $requireType): array
    {
        $data = $request->validate([
            'letter_type' => [$requireType ? 'required' : 'nullable', 'in:I,II,III,IV,V'],
            'agenda_series' => ['nullable', 'string', 'max:10'],
            'letter_no' => ['required', 'string', 'max:50'],
            'director_id' => ['nullable', 'exists:directors,id'],
            'subject' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'letter_date' => ['nullable', 'date'],
            'received_date' => ['nullable', 'date'],
            'pages' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:50'],
            'sender_unit_id' => ['nullable', 'exists:sender_units,id'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'director_recipients' => ['nullable', 'array'],
            'director_recipients.*' => ['exists:directors,id'],
            'department_recipients' => ['nullable', 'array'],
            'department_recipients.*' => ['exists:departments,id'],
        ]);

        $data['location'] = isset($data['location']) ? Str::upper($data['location']) : null;

        return $data;
    }

    private function formOptions(): array
    {
        return [
            'directors' => Director::where('is_active', true)->orderBy('name')->get(['id', 'name', 'code']),
            'departments' => Department::where('is_active', true)->orderBy('sort_order')->get(['id', 'code', 'name']),
            'senderUnits' => SenderUnit::orderBy('name')->get(['id', 'name', 'abbr']),
        ];
    }

}
