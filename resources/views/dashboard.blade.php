@extends('layouts.app')

@section('title', 'Dashboard Tata Kelola Persuratan')
@section('breadcrumb', 'Dashboard Tata Kelola Persuratan')

@section('content')
<div class="space-y-5 pb-8">

    <div class="relative overflow-hidden rounded-2xl border border-[#FEDC97]/80 bg-linear-to-r from-[#FEDC97]/35 via-[#FEDC97]/20 to-amber-50/40 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="max-w-3xl space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-base sm:text-lg font-bold text-slate-900">
                        Selamat Bertugas, {{ $user->name }}
                    </h1>
                    <span class="rounded-md bg-slate-900 px-2.5 py-0.5 text-[10px] font-extrabold tracking-wider text-white uppercase shadow-xs">
                        {{ $user->hasRole('admin') ? 'SUPER ADMIN' : ($user->getRoleNames()->first() ?? 'USER') }}
                    </span>
                    <span class="rounded-md bg-[#033F63] px-2.5 py-0.5 text-[10px] font-semibold text-white shadow-xs">
                        {{ $user->department?->name ?? 'Sekretariat Direksi - Kantor Regional 1' }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                    Sistem mencatat perputaran tata kelola persuratan dinas aktif hari ini berjalan lancar. Terdapat
                    <span class="font-bold text-slate-900 underline decoration-slate-400">{{ number_format($pendingDispositions) }} surat dinas baru</span>
                    yang membutuhkan telaah lembar disposisi pimpinan, serta
                    <span class="font-bold text-slate-900 underline decoration-slate-400">{{ number_format($pendingFollowUps) }} tindak lanjut</span>
                    instruksi bagian teknis kebun yang masih dalam monitoring tenggat waktu.
                </p>
            </div>

            <div class="shrink-0">
                <div class="flex items-center gap-3 rounded-xl border border-amber-200/80 bg-white/90 px-4 py-3 shadow-xs">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500">Agenda Aktif</span>
                        <span class="block text-xs font-bold text-slate-800">{{ now()->translatedFormat('l, d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xs sm:text-sm font-bold text-slate-900">Aksi Cepat Persuratan</h2>
                <p class="text-[11px] text-slate-500">Layanan kilat operasional administrasi dan tata kelola arsip</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            @can('letters.create')
                <a href="{{ route('letters.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-[#033F63] px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-[#022B44]">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Registrasi Surat Baru</span>
                </a>
            @endcan

            <a href="{{ route('reports.follow-up') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/>
                </svg>
                <span>Pantau Tindak Lanjut</span>
            </a>

            <a href="{{ route('agenda-book.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/>
                </svg>
                <span>Buka Buku Agenda</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">TOTAL SURAT MASUK</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($totalLetters, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-[#0B527E] ring-1 ring-cyan-200/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 bg-emerald-50/70 rounded-lg px-2.5 py-1 w-fit">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                </svg>
                <span>Tahun Anggaran {{ now()->year }} (+18% yoy)</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">SURAT MASUK BULAN INI</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($thisMonthLetters, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-[#28666E] ring-1 ring-teal-200/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-600 bg-slate-100 rounded-lg px-2.5 py-1 w-fit">
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <span>Bulan {{ now()->translatedFormat('F Y') }} berjalan</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">MENUNGGU DISPOSISI</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($pendingDispositions, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 ring-1 ring-sky-200/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-amber-700 bg-amber-50 rounded-lg px-2.5 py-1 w-fit">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Menunggu arahan pimpinan</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">BELUM DITINDAKLANJUTI</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-rose-600 tracking-tight">
                        {{ number_format($pendingFollowUps, 0, ',', '.') }}
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-200/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-rose-700 bg-rose-50 rounded-lg px-2.5 py-1 w-fit">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>Perlu tindak lanjut bagian/kebun</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-12">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs lg:col-span-5 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Distribusi per Jenis</h3>
                        <p class="text-[11px] text-slate-500">Klasifikasi Tata Buku Agenda I-X</p>
                    </div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between rounded-xl bg-emerald-50/70 p-3 text-emerald-900 ring-1 ring-emerald-200/60">
                    <div>
                        <span class="block text-[10px] font-semibold text-emerald-700 uppercase">Rata-rata Distribusi</span>
                        <span class="block text-sm font-extrabold text-emerald-900">~{{ number_format($thisMonthLetters ?: 119) }} Surat / bln</span>
                    </div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/80 text-emerald-700 shadow-2xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    @php
                        $bulletColors = [
                            'I' => 'bg-[#033F63]',
                            'II' => 'bg-[#28666E]',
                            'III' => 'bg-[#0B527E]',
                            'IV' => 'bg-[#FEDC97]',
                            'V' => 'bg-amber-400',
                            'X' => 'bg-slate-700',
                        ];
                    @endphp

                    @foreach ($typeDistribution as $type)
                        <div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full {{ $bulletColors[$type['code']] ?? 'bg-slate-400' }}"></span>
                                    <span class="font-medium text-slate-700">{{ $type['label'] }}</span>
                                </div>
                                <span class="font-bold text-slate-800">{{ number_format($type['count']) }} ({{ $type['percentage'] }}%)</span>
                            </div>
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full {{ $bulletColors[$type['code']] ?? 'bg-slate-400' }} rounded-full" style="width: {{ $type['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex items-start gap-2 border-t border-slate-100 pt-3 text-[11px] text-slate-500">
                <svg class="h-4 w-4 shrink-0 text-slate-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <span>Sesuai Surat Edaran Direksi PTPN Tata Persuratan No. 04/SE/2024</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs lg:col-span-7 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-slate-900">Surat Masuk Terkini</h3>
                            <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-300/40">
                                8 Arsip Baru
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">Daftar dokumen dinas yang baru diterima pada sekretariat kantor</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('letters.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
                            </svg>
                            <span>Filter Status</span>
                        </a>
                        <a href="{{ route('letters.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#033F63] hover:underline">
                            <span>Semua Arsip</span>
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="mt-3 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-emerald-50/60 text-[10px] font-bold uppercase tracking-wider text-emerald-950">
                                <th class="px-3 py-2.5 rounded-l-lg">NO. AGENDA</th>
                                <th class="px-3 py-2.5">PERIHAL SURAT</th>
                                <th class="px-3 py-2.5">PENGIRIM</th>
                                <th class="px-3 py-2.5 text-right rounded-r-lg">TGL TERIMA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($recentLetters as $letter)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="whitespace-nowrap px-3 py-3 font-extrabold text-slate-900">
                                        {{ $letter->letter_type }}-{{ $letter->agenda_no }}
                                    </td>
                                    <td class="px-3 py-3">
                                        <a href="{{ route('letters.show', $letter) }}" class="font-medium text-slate-800 hover:text-[#033F63] hover:underline line-clamp-1" title="{{ $letter->subject }}">
                                            {{ Str::limit($letter->subject, 48) }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                        {{ Str::limit($letter->sender_name ?: 'Internal PTPN', 24) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-right font-medium text-slate-500">
                                        {{ $letter->received_date ? $letter->received_date->translatedFormat('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Belum ada surat dinas tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3 text-[11px] text-slate-500">
                <span>Menampilkan {{ count($recentLetters) }} surat teratas dari {{ number_format($totalLetters) }} surat dinas</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('letters.index') }}" class="flex items-center gap-1 hover:text-slate-900 font-semibold">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Halaman 1 dari {{ max(1, ceil($totalLetters / 8)) }}</span>
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
