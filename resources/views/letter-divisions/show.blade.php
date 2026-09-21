@extends('layouts.app')

@section('title', 'Detail Surat Bagian')
@section('breadcrumb', 'Surat Bagian / Detail')

@section('content')
    <div class="mx-auto max-w-4xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ $division->letter_no ?? '(tanpa nomor surat)' }}</h1>
                <p class="text-sm text-slate-500">Agenda {{ $division->agenda_no }}/{{ $division->agenda_type_code }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('letter-divisions.edit', $division) }}" class="text-sm text-emerald-600 hover:underline">Edit</a>
                <a href="{{ route('letter-divisions.index') }}" class="text-sm text-emerald-600 hover:underline">&larr; Kembali</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kepada</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->addressee ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dari</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->sender_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tgl Surat</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->letter_date?->format('d-m-Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tgl Terima</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->received_date?->format('d-m-Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jumlah Lembar</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->amount ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->status ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hal</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $division->subject }}</dd>
                </div>
                @if ($division->matter)
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Urusan</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $division->matter }}</dd>
                    </div>
                @endif
                @if ($division->note)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $division->note }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($division->directorRecipients->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Tembusan Direksi</h2>
                <ul class="space-y-1 text-sm text-slate-700">
                    @foreach ($division->directorRecipients as $director)
                        <li>{{ $director->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-slate-900">Riwayat Disposisi</h2>

            @forelse ($division->dispositions as $disposition)
                <div class="border-b border-slate-100 py-2 text-sm last:border-0">
                    <span class="font-medium text-slate-900">{{ $disposition->dispositionType?->label }}</span>
                    <span class="text-slate-500"> &mdash; {{ $disposition->actor_role === 'department-head' ? 'Kabag' : 'Direksi' }},
                        oleh {{ $disposition->creator?->name ?? '-' }}, {{ $disposition->disposed_at?->format('d-m-Y H:i') }}</span>
                    @if ($disposition->note)
                        <p class="mt-1 text-slate-600">{{ $disposition->note }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada disposisi.</p>
            @endforelse

            <form method="POST" action="{{ route('letter-divisions.dispositions.store', $division) }}" class="mt-4 border-t border-slate-100 pt-4">
                @csrf

                <p class="text-sm font-medium text-slate-700">Tambah Instruksi Disposisi</p>
                <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach ($dispositionTypes as $type)
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="disposition_type_ids[]" value="{{ $type->id }}"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            {{ $type->label }}
                        </label>
                    @endforeach
                </div>

                <textarea name="note" rows="2" placeholder="Catatan (opsional)"
                          class="mt-3 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>

                <button class="mt-3 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan Disposisi
                </button>
            </form>
        </div>
    </div>
@endsection
