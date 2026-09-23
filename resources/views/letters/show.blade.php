@extends('layouts.app')

@section('title', "Detail Surat: {$letter->letter_no}")
@section('breadcrumb', "Arsip Surat Masuk / Detail Agenda {$letter->letter_type}-{$letter->agenda_no}{$letter->agenda_series}")

@section('content')
    @php
        $typeNames = [
            'I' => 'Direksi / Korporat',
            'II' => 'Operasional',
            'III' => 'Keuangan',
            'IV' => 'Hukum & HGU',
            'V' => 'SDM & Umum',
            'X' => 'Khusus',
        ];
        $typeDescriptions = [
            'I' => 'Surat Keputusan & Arahan Direksi/Holding',
            'II' => 'Instruksi Operasional Tanaman & Pabrik',
            'III' => 'Keuangan, Perpajakan & Kas',
            'IV' => 'Hukum, Pertanahan & HGU',
            'V' => 'SDM, Perlengkapan & Umum',
            'X' => 'Klasifikasi Khusus / Terbatas',
        ];
        $typeName = $typeNames[$letter->letter_type] ?? 'Umum';
        $typeDesc = $typeDescriptions[$letter->letter_type] ?? 'Surat Dinas Masuk';

        $slaDate = $letter->received_date ? $letter->received_date->copy()->addDays(7) : null;
        $isOverdue = ! $letter->follow_up && $slaDate && now()->startOfDay()->gt($slaDate);
        $remainingDays = $slaDate ? now()->startOfDay()->diffInDays($slaDate, false) : 0;

        $keywords = $letter->keyword ? array_filter(array_map('trim', explode(',', $letter->keyword))) : [];
    @endphp

    <div class="space-y-6 pb-12">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('letters.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-primary transition pr-2 border-r border-slate-200">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    <span>Kembali ke Arsip</span>
                </a>

                <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold bg-sky-50 text-sky-800 border border-sky-200/80 shadow-2xs">
                    Agenda {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}
                </span>

                @if ($letter->follow_up)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-2xs">
                        <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Selesai Ditindaklanjuti</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/80 shadow-2xs">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span>Dalam Proses</span>
                    </span>
                @endif

                @if ($isOverdue)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs">
                        <span>!</span>
                        <span>Sangat Segera (SLA Terlewat)</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                        <span>●</span>
                        <span>Biasa / Normal</span>
                    </span>
                @endif

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                    @if ($letter->letter_type === 'X')
                        <svg class="h-3 w-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <span>Rahasia</span>
                    @else
                        <svg class="h-3 w-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                        </svg>
                        <span>Dinas Biasa</span>
                    @endif
                </span>
            </div>

            <div class="flex items-center gap-2">
                <x-button variant="secondary" href="{{ route('letters.print', $letter) }}" target="_blank">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    <span>Cetak Lembar Disposisi</span>
                </x-button>

                @can('letters.update')
                    @if (auth()->user()->can('letters.view-all') || $letter->created_by === auth()->id())
                        <x-button variant="secondary" href="{{ route('letters.edit', $letter) }}">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span>Edit Surat</span>
                        </x-button>

                        <x-button
                            type="button"
                            variant="danger"
                            @click="$dispatch('open-confirm-modal', {
                                title: 'Hapus Arsip Surat Masuk',
                                message: 'Apakah Anda yakin ingin menghapus arsip surat {{ addslashes($letter->letter_no ?? '-') }}? Data surat beserta seluruh disposisinya akan terhapus.',
                                confirmText: 'Hapus Surat',
                                variant: 'danger',
                                action: '{{ route('letters.destroy', $letter) }}',
                                method: 'DELETE'
                            })"
                        >
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                            <span>Hapus</span>
                        </x-button>
                    @endif
                @endcan
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Informasi Surat Dinas</h2>
                                <p class="text-[11px] text-slate-400">Identitas dan data registrasi surat masuk</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                            Jenis {{ $letter->letter_type }} ({{ $typeName }})
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">NO. AGENDA</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-800">
                                {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">KLASIFIKASI AGENDA</dt>
                            <dd class="mt-1 text-xs font-medium text-slate-700">
                                Jenis {{ $letter->letter_type }} - {{ $typeDesc }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">NO. SURAT ASAL</dt>
                            <dd class="mt-1 text-sm font-bold text-slate-900 break-words">
                                {{ $letter->letter_no }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">TANGGAL SURAT</dt>
                            <dd class="mt-1 text-xs font-semibold text-slate-700">
                                {{ $letter->letter_date?->translatedFormat('d F Y') ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">TANGGAL DITERIMA</dt>
                            <dd class="mt-1 flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <span>{{ $letter->received_date?->translatedFormat('d F Y') ?? '-' }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">DIREKTUR TUJUAN</dt>
                            <dd class="mt-1 text-xs font-bold text-primary">
                                {{ $letter->director?->name ?? '-' }}
                            </dd>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">ASAL PENGIRIM</dt>
                        <dd class="mt-1.5">
                            <div class="text-sm font-bold text-slate-900">
                                {{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}
                            </div>
                            @if ($letter->senderUnit?->abbr)
                                <div class="text-xs text-slate-500 mt-0.5 font-medium">
                                    {{ $letter->senderUnit->abbr }}
                                </div>
                            @endif
                        </dd>
                    </div>

                    <div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/60 p-4.5">
                        <div class="text-[10.5px] font-extrabold uppercase tracking-wider text-emerald-800 mb-1.5">
                            PERIHAL
                        </div>
                        <div class="text-sm font-bold text-slate-900 leading-relaxed">
                            {{ $letter->subject }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4.5">
                        <div class="flex items-center gap-1.5 text-[10.5px] font-extrabold uppercase tracking-wider text-slate-500 mb-2">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="21" y1="10" x2="3" y2="10"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="14" x2="3" y2="14"/><line x1="21" y1="18" x2="3" y2="18"/>
                            </svg>
                            <span>RINGKASAN ISI SURAT / CATATAN</span>
                        </div>
                        <div class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                            {{ $letter->content ?: 'Tidak ada ringkasan atau catatan tambahan untuk surat ini.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/>
                                <line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/>
                                <line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>
                            </svg>
                            <h3 class="text-xs font-bold text-slate-900">Status Tindak Lanjut Surat</h3>
                        </div>
                        @if ($slaDate)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10.5px] font-bold {{ $isOverdue ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' }}">
                                SLA: {{ $slaDate->format('d M Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $letter->follow_up ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/60' }}">
                        <div class="flex items-start gap-2.5">
                            <span class="mt-1 flex h-2 w-2 rounded-full {{ $letter->follow_up ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">
                                    {{ $letter->follow_up ? 'Selesai Ditindaklanjuti' : 'Dalam Proses Tindak Lanjut' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    @if ($letter->follow_up)
                                        Seluruh instruksi &amp; tindak lanjut telah diselesaikan
                                    @elseif ($isOverdue)
                                        Tenggat SLA terlewat: {{ $slaDate?->format('d M Y') }}
                                    @else
                                        Tenggat SLA: {{ $slaDate?->format('d M Y') }} (sisa {{ max(0, $remainingDays) }} hari)
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($letter->follow_up)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    <span>Selesai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    <span>Dalam Proses</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 flex items-start gap-1.5">
                        <svg class="h-3.5 w-3.5 shrink-0 text-slate-400 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <span>Status tindak lanjut dan kelengkapan data diperbarui melalui menu <strong>Edit Surat</strong>.</span>
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <h3 class="text-xs font-bold text-slate-900">Distribusi &amp; Tembusan</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Carbon Copy (CC)</span>
                    </div>

                    <div class="space-y-2">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tembusan Direksi / SEVP</div>
                        @if ($letter->directorRecipients->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($letter->directorRecipients as $director)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80 shadow-2xs"
                                          title="{{ $director->name }}">
                                        <span class="font-bold">{{ $director->abbr ?: $director->code }}</span>
                                        <span class="text-[11px] text-emerald-600/90 font-normal">({{ $director->name }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Tidak ada tembusan Direksi / SEVP.</p>
                        @endif
                    </div>

                    <div class="space-y-2 pt-3 border-t border-slate-100">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Tembusan Bagian / Unit Kerja</div>
                        @if ($letter->departmentRecipients->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($letter->departmentRecipients as $dept)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/80 shadow-2xs">
                                        {{ $dept->name ?: $dept->code }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">Tidak ada tembusan Bagian / Unit Kerja.</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                            </svg>
                            <h3 class="text-xs font-bold text-slate-900">Tata Kelola Arsip Fisik &amp; Indeks</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Metadata</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                            <dt class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                                <span>LOKASI SIMPAN</span>
                            </dt>
                            <dd class="mt-1 text-xs font-bold text-slate-800">
                                {{ $letter->location ?: '-' }}
                            </dd>
                        </div>

                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/60">
                            <dt class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                </svg>
                                <span>JUMLAH HALAMAN</span>
                            </dt>
                            <dd class="mt-1 text-xs font-bold text-slate-800">
                                {{ $letter->pages ? "{$letter->pages} Lembar / Hlm" : '-' }}
                            </dd>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <svg class="h-3 w-3 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                            </svg>
                            <span>KATA KUNCI PENCARIAN (KEYWORD)</span>
                        </dt>
                        <dd class="flex flex-wrap gap-1.5">
                            @if (!empty($keywords))
                                @foreach ($keywords as $kw)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        #{{ $kw }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-slate-400 italic">Tidak ada kata kunci khusus.</span>
                            @endif
                        </dd>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.48-8.48"/>
                            </svg>
                            <h3 class="text-xs font-bold text-slate-900">Berkas Lampiran Digital</h3>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">PDF / Dokumen</span>
                    </div>

                    @if ($letter->attachment_path)
                        <div class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-white hover:border-slate-300 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 border border-rose-200/60 font-black text-[11px]">
                                    PDF
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate" title="{{ basename($letter->attachment_path) }}">
                                        {{ basename($letter->attachment_path) }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-flex items-center gap-1 text-[10px] text-emerald-600 font-semibold">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                            <span>Terverifikasi Aman</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($letter->attachment_path) }}" target="_blank"
                               class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-400 hover:text-primary hover:bg-slate-100 transition"
                               title="Unduh berkas lampiran">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                            </a>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">Tidak ada berkas digital terlampir.</p>
                    @endif

                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-400">
                        <div class="flex items-center justify-between">
                            <span>Petugas Registrasi:</span>
                            <strong class="text-slate-700 font-semibold">{{ $letter->creator?->name ?? 'Sistem' }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Waktu Registrasi:</span>
                            <span class="text-slate-600 font-medium">{{ $letter->created_at?->translatedFormat('d M Y • H:i') ?? '-' }} WIB</span>
                        </div>
                        @if ($letter->updated_at && $letter->updated_at->ne($letter->created_at))
                            <div class="flex items-center justify-between">
                                <span>Terakhir Diperbarui:</span>
                                <span class="text-slate-600 font-medium">{{ $letter->updated_at->translatedFormat('d M Y • H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
