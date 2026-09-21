@extends('layouts.app')

@section('title', 'Detail Surat')
@section('breadcrumb', 'Arsip Surat / Detail')

@section('content')
    @php
        $telat = ! $letter->follow_up
            && $letter->received_date
            && $letter->received_date->diffInDays(now()) >= 7;
    @endphp

    <div class="mx-auto max-w-4xl space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">{{ $letter->letter_no }}</h1>
                <p class="text-sm text-slate-500">Agenda {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('letters.print', $letter) }}" target="_blank" class="text-sm text-emerald-600 hover:underline">Cetak Lembar Disposisi</a>
                @can('letters.update')
                    @if (auth()->user()->can('letters.view-all') || $letter->created_by === auth()->id())
                        <a href="{{ route('letters.edit', $letter) }}" class="text-sm text-emerald-600 hover:underline">Edit</a>
                    @endif
                @endcan
                <a href="{{ route('letters.index') }}" class="text-sm text-emerald-600 hover:underline">&larr; Kembali ke Arsip</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kepada</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->director?->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dari</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tgl Surat</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->letter_date?->format('d-m-Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tgl Terima</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->received_date?->format('d-m-Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jumlah Lembar</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->pages ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Lokasi Penyimpanan</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->location ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hal</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->subject }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kata Kunci</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ $letter->keyword ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status Tindak Lanjut</dt>
                    <dd class="mt-1">
                        @if ($letter->follow_up)
                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Sudah Ditindaklanjuti</span>
                        @elseif ($telat)
                            <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Belum Ditindaklanjuti (&ge; 7 hari)</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Belum Ditindaklanjuti</span>
                        @endif
                    </dd>
                </div>
                @if ($letter->content)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan</dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-slate-900">{{ $letter->content }}</dd>
                    </div>
                @endif
                @if ($letter->attachment_path)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Lampiran</dt>
                        <dd class="mt-1 text-sm">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($letter->attachment_path) }}" target="_blank" class="text-emerald-600 hover:underline">Lihat Lampiran</a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        @if ($letter->directorRecipients->isNotEmpty() || $letter->departmentRecipients->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Tujuan Tambahan</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @if ($letter->directorRecipients->isNotEmpty())
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Direksi</p>
                            <ul class="mt-1 space-y-1 text-sm text-slate-700">
                                @foreach ($letter->directorRecipients as $director)
                                    <li>{{ $director->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($letter->departmentRecipients->isNotEmpty())
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bagian</p>
                            <ul class="mt-1 space-y-1 text-sm text-slate-700">
                                @foreach ($letter->departmentRecipients as $department)
                                    <li>{{ $department->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-3 text-sm font-semibold text-slate-900">Riwayat Disposisi</h2>

            @forelse ($letter->dispositions as $disposition)
                <div class="flex items-start justify-between border-b border-slate-100 py-2 text-sm last:border-0">
                    <div>
                        <span class="font-medium text-slate-900">{{ $disposition->dispositionType?->label }}</span>
                        <span class="text-slate-500"> &mdash; oleh {{ $disposition->creator?->name ?? '-' }}, {{ $disposition->disposed_at?->format('d-m-Y H:i') }}</span>
                        @if ($disposition->note)
                            <p class="mt-1 text-slate-600">{{ $disposition->note }}</p>
                        @endif
                    </div>

                    @can('letters.dispose')
                        @if (auth()->user()->can('letters.view-all') || $disposition->created_by === auth()->id())
                            <form method="POST" action="{{ route('letters.dispositions.destroy', [$letter, $disposition]) }}"
                                  onsubmit="return confirm('Hapus disposisi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs text-red-600 hover:underline">Hapus</button>
                            </form>
                        @endif
                    @endcan
                </div>
            @empty
                <p class="text-sm text-slate-400">Belum ada disposisi.</p>
            @endforelse

            @can('letters.dispose')
                <form method="POST" action="{{ route('letters.dispositions.store', $letter) }}" class="mt-4 border-t border-slate-100 pt-4">
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
            @endcan
        </div>

        <div class="text-xs text-slate-400">
            Dibuat oleh {{ $letter->creator?->name ?? '-' }} &middot; {{ $letter->created_at?->format('d-m-Y H:i') }}
        </div>
    </div>
@endsection
