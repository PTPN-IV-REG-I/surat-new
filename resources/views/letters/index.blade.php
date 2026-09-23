@extends('layouts.app')

@section('title', 'Arsip Surat Masuk')
@section('breadcrumb', 'Arsip Surat Masuk')

@section('content')
<div class="space-y-6 pb-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Arsip Surat Masuk</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola, cari, dan telusuri seluruh arsip surat dinas masuk korporat PT Perkebunan Nusantara</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <x-button variant="secondary" href="{{ route('letters.export', request()->query()) }}">
                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Ekspor Excel / CSV</span>
            </x-button>

            @can('letters.create')
                <x-button variant="primary" href="{{ route('letters.create') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Registrasi Surat</span>
                </x-button>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">TOTAL ARSIP</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($totalCount, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">surat</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-[#0B527E] ring-1 ring-cyan-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-700">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                </svg>
                <span>+12% yoy</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">MENUNGGU DISPOSISI</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($pendingDispositionsCount, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">surat</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200/60">
                Perlu Respons Cepat
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">DALAM TINDAK LANJUT</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($inProgressFollowUpCount, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">surat</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600 ring-1 ring-cyan-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3a4 4 0 0 0-1.172-2.872L3 3"/><path d="m15 9 6-6"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-cyan-50 px-2.5 py-0.5 text-[10px] font-bold text-cyan-700 ring-1 ring-cyan-200/60">
                Proses Koordinasi
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">SELESAI DIARSIPKAN</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($completedCount, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">surat</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200/60">
                Tersimpan di Dokumen
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs">
        <form method="GET" action="{{ route('letters.index') }}"
              class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3"
              x-data="{ search: '{{ addslashes(request('search', '')) }}' }">
            <div class="relative flex-1 min-w-[220px]">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <input type="text" name="search" x-model="search"
                       placeholder="Cari no. surat, perihal, pengirim, direktur..."
                       class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 pl-10 pr-9 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                <button type="button"
                        x-show="search.length > 0"
                        x-cloak
                        @click="search = ''; $nextTick(() => $el.closest('form').submit())"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition cursor-pointer"
                        title="Hapus kata kunci pencarian">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <x-datepicker
                    name="date"
                    placeholder="Pilih rentang tanggal..."
                    mode="range"
                    wrapperClass="w-full sm:w-60"
                />

                <x-select name="type" wrapperClass="w-full sm:w-48">
                    <option value="">Semua Jenis (I - X)</option>
                    <option value="I" {{ request('type') == 'I' ? 'selected' : '' }}>Jenis I (Direksi)</option>
                    <option value="II" {{ request('type') == 'II' ? 'selected' : '' }}>Jenis II (Operasional)</option>
                    <option value="III" {{ request('type') == 'III' ? 'selected' : '' }}>Jenis III (Keuangan)</option>
                    <option value="IV" {{ request('type') == 'IV' ? 'selected' : '' }}>Jenis IV (Hukum/HGU)</option>
                    <option value="V" {{ request('type') == 'V' ? 'selected' : '' }}>Jenis V (SDM/Umum)</option>
                    <option value="X" {{ request('type') == 'X' ? 'selected' : '' }}>Jenis X (Khusus)</option>
                </x-select>

                <x-select name="status" wrapperClass="w-full sm:w-44">
                    <option value="">Semua Status</option>
                    <option value="pending_disposition" {{ request('status') == 'pending_disposition' ? 'selected' : '' }}>Menunggu Disposisi</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Selesai Disposisi</option>
                    <option value="need_followup" {{ request('status') == 'need_followup' ? 'selected' : '' }}>Perlu Tindak Lanjut</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai Diarsipkan</option>
                </x-select>

                <x-button type="submit" variant="primary" class="w-full sm:w-auto">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Cari</span>
                </x-button>

                @if (request()->hasAny(['search', 'date', 'type', 'status', 'month', 'year']))
                    <x-button variant="danger" href="{{ route('letters.index') }}" title="Reset semua filter" class="w-full sm:w-auto">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        <span>Reset</span>
                    </x-button>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[1380px]">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-[#EAF5EF] text-[10.5px] font-extrabold uppercase tracking-wider text-emerald-950">
                        <th class="px-3 py-3.5 w-12 text-center">NO</th>
                        <th class="px-4 py-3.5 w-28">NO. AGENDA</th>
                        <th class="px-4 py-3.5 w-44">TGL SURAT &amp; TERIMA</th>
                        <th class="px-4 py-3.5 w-52">NO. SURAT</th>
                        <th class="px-4 py-3.5 w-56">DARI &amp; KEPADA</th>
                        <th class="px-4 py-3.5 min-w-[260px]">PERIHAL &amp; KATA KUNCI</th>
                        <th class="px-4 py-3.5 w-56">DISPOSISI</th>
                        <th class="px-4 py-3.5 w-96 min-w-[340px]">CATATAN</th>
                        <th class="px-3 py-3.5 w-16 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($letters as $letter)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-3 py-4 text-center align-top font-bold text-slate-400 text-xs">
                                {{ $letters->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-800 ring-1 ring-emerald-200">
                                    {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}
                                </span>
                                <div class="mt-1 text-[10px] text-slate-400 font-semibold tracking-wider uppercase">
                                    Jenis {{ $letter->letter_type }}
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top whitespace-nowrap text-xs">
                                <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                                    <span class="text-[10px] text-slate-400 font-normal w-12">Surat:</span>
                                    <span>{{ $letter->letter_date ? $letter->letter_date->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-slate-700 font-semibold mt-1">
                                    <span class="text-[10px] text-slate-400 font-normal w-12">Terima:</span>
                                    <span class="text-[#033F63] font-bold">{{ $letter->received_date ? $letter->received_date->format('d/m/Y') : '-' }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <div class="font-bold text-slate-900 text-xs leading-snug break-words">
                                    {{ $letter->letter_no }}
                                </div>
                                @if ($letter->keyword)
                                    <div class="mt-1.5 inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                        <svg class="h-2.5 w-2.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                                        <span class="truncate max-w-[140px]" title="{{ $letter->keyword }}">{{ $letter->keyword }}</span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 align-top text-xs">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dari:</span>
                                    <div class="font-bold text-slate-900 text-xs mt-0.5 leading-snug">
                                        {{ $letter->senderUnit?->name ?? $letter->sender_name ?? 'Internal PTPN' }}
                                    </div>
                                </div>
                                <div class="mt-2 pt-2 border-t border-slate-100">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Kepada:</span>
                                    <div class="inline-flex items-center mt-0.5 font-bold text-xs text-[#033F63] bg-sky-50 px-2 py-0.5 rounded ring-1 ring-sky-200/60">
                                        {{ $letter->director?->name ?? $letter->director?->abbr ?? 'Pimpinan' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <a href="{{ route('letters.show', $letter) }}"
                                   class="font-semibold text-xs text-slate-900 hover:text-[#033F63] hover:underline block leading-snug"
                                   title="{{ $letter->subject }}">
                                    {{ $letter->subject }}
                                </a>

                                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                    @php
                                        $haystack = strtolower($letter->subject . ' ' . $letter->keyword);
                                        $isSangatSegera = str_contains($haystack, 'sangat segera');
                                        $isSegera = $isSangatSegera ? false : (str_contains($haystack, 'segera') || !$letter->follow_up);
                                        $isRahasia = str_contains($haystack, 'rahasia') || in_array($letter->letter_type, ['I', 'X']);
                                    @endphp

                                    @if ($isSangatSegera)
                                        <span class="rounded bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600 ring-1 ring-rose-200/80">Sangat Segera</span>
                                    @elseif ($isSegera)
                                        <span class="rounded bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200/80">Segera</span>
                                    @else
                                        <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 ring-1 ring-emerald-200/80">Biasa</span>
                                    @endif

                                    @if ($isRahasia)
                                        <span class="rounded bg-slate-900 px-2 py-0.5 text-[10px] font-bold text-white">Rahasia</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top text-xs">
                                @php
                                    $dispositionsList = $letter->dispositions->pluck('dispositionType.label')->filter()->unique();
                                    $recipients = $letter->departmentRecipients->pluck('code')->filter()->unique();
                                    if ($recipients->isEmpty()) {
                                        $recipients = $letter->directorRecipients->pluck('abbr')->filter()->unique();
                                    }
                                @endphp

                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tujuan:</span>
                                    <div class="font-bold text-slate-800 mt-0.5 leading-snug">
                                        {{ $recipients->isNotEmpty() ? $recipients->join(', ') : '-' }}
                                    </div>
                                </div>

                                <div class="mt-1.5">
                                    @if ($dispositionsList->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($dispositionsList as $dispLabel)
                                                <span class="inline-block rounded bg-cyan-50 px-1.5 py-0.5 text-[10px] font-semibold text-cyan-800 ring-1 ring-cyan-200/70">
                                                    {{ $dispLabel }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum ada</span>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    @if ($letter->follow_up)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Selesai
                                        </span>
                                    @elseif ($letter->dispositions->isNotEmpty())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-2 py-0.5 text-[10px] font-bold text-cyan-700 ring-1 ring-cyan-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
                                            Ditindaklanjuti
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                @php
                                    $noteText = $letter->content ?: $letter->dispositions->where('note', '!=', '')->pluck('note')->first();
                                @endphp
                                @if ($noteText)
                                    <div class="w-full max-w-[360px] rounded-xl bg-slate-50/90 p-2.5 border border-slate-200/80 text-[11px] text-slate-700 font-mono leading-relaxed break-words" title="{{ $noteText }}">
                                        {{ $noteText }}
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <td class="px-3 py-4 align-top text-center whitespace-nowrap">
                                <div class="flex flex-col items-center justify-center gap-1.5 w-fit mx-auto">
                                    <a href="{{ route('letters.show', $letter) }}" title="Lihat Detail Surat"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-600 hover:bg-sky-100 hover:border-sky-300 hover:text-sky-700 shadow-2xs transition">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    @can('letters.update')
                                        @if (auth()->user()->can('letters.view-all') || $letter->created_by === auth()->id())
                                            <a href="{{ route('letters.edit', $letter) }}" title="Ubah / Edit Surat"
                                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 hover:border-amber-300 shadow-2xs transition">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </a>

                                            {{-- Tombol Hapus Surat via Confirm Alert Modal --}}
                                            <button
                                                type="button"
                                                @click="$dispatch('open-confirm-modal', {
                                                    title: 'Hapus Arsip Surat Masuk',
                                                    message: 'Apakah Anda yakin ingin menghapus arsip surat nomor {{ addslashes($letter->letter_no ?? '-') }}? Tindakan ini tidak dapat dibatalkan.',
                                                    confirmText: 'Hapus Surat',
                                                    variant: 'danger',
                                                    action: '{{ route('letters.destroy', $letter) }}',
                                                    method: 'DELETE'
                                                })"
                                                title="Hapus Surat"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                            >
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-slate-400">
                                Tidak ada arsip surat yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 px-5 py-4 bg-white text-xs text-slate-600">
            <div>
                Menampilkan <span class="font-bold text-slate-800">{{ $letters->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $letters->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ number_format($letters->total(), 0, ',', '.') }}</span> surat
            </div>

            <div class="flex items-center gap-2">
                <span class="text-slate-500 text-xs">Baris per halaman:</span>
                <x-select name="per_page" onchange="window.location.href = this.value;" wrapperClass="w-20">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $letters->perPage() == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </x-select>
            </div>

            <div>
                {{ $letters->links('letters._pagination') }}
            </div>
        </div>
    </div>

</div>
@endsection
