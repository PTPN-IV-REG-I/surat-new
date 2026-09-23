@extends('layouts.app')

@section('title', 'Buku Agenda Surat Masuk')
@section('breadcrumb', 'Buku Agenda')

@section('content')
    <div class="w-full space-y-6 pb-12"
         x-data="{
             activeAccordion: '{{ $letterType ?: ($letters->keys()->first() ?? 'I') }}',
             viewMode: 'detailed',
             searchTerms: {},
             toggleAccordion(key) {
                 this.activeAccordion = this.activeAccordion === key ? null : key;
             },
             openAccordion(key) {
                 this.activeAccordion = key;
                 this.$nextTick(() => {
                     const el = document.getElementById('accordion-' + key);
                     if (el) {
                         el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                     }
                 });
             },
             matches(text, query) {
                 if (!query || query.trim() === '') return true;
                 return text.toLowerCase().includes(query.toLowerCase().trim());
             }
         }">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#033F63] text-white shadow-xs">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        <line x1="8" y1="6" x2="16" y2="6"/>
                        <line x1="8" y1="10" x2="16" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        Buku Agenda Surat Masuk
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5 max-w-2xl leading-relaxed">
                        Buku register dan penomoran urut surat dinas masuk korporat berdasarkan klasifikasi tata naskah dinas PT Perkebunan Nusantara.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-0.5 shadow-2xs">
                    <button type="button"
                            @click="viewMode = 'detailed'"
                            :class="viewMode === 'detailed' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition"
                            title="Format Lengkap (Sesuai Buku Agenda Dinas / View Lama)">
                        <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/>
                        </svg>
                        <span>Format Lengkap</span>
                    </button>
                    <button type="button"
                            @click="viewMode = 'compact'"
                            :class="viewMode === 'compact' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition"
                            title="Format Ringkas">
                        <svg class="h-3.5 w-3.5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                        <span>Ringkas</span>
                    </button>
                </div>

                <button type="button"
                        onclick="window.print()"
                        class="inline-flex h-9.5 items-center gap-2 rounded-xl bg-[#033F63] px-3.5 text-xs font-bold text-white shadow-xs transition hover:bg-[#022B44]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    <span>Cetak Register</span>
                </button>

                <a href="{{ route('letters.export', array_filter(['year' => $year, 'month' => $month, 'letter_type' => $letterType])) }}"
                   class="inline-flex h-9.5 items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 text-xs font-bold text-sky-800 shadow-2xs transition hover:bg-sky-100">
                    <svg class="h-4 w-4 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="8" y1="13" x2="16" y2="13"/>
                        <line x1="8" y1="17" x2="16" y2="17"/>
                    </svg>
                    <span>Ekspor XLSX</span>
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-3.5 shadow-xs">
            <form id="agenda-filter-form" method="GET" action="{{ route('agenda-book.index') }}"
                  class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 w-full">
                <div class="flex-1 min-w-[150px]">
                    <x-select name="month" wrapperClass="w-full">
                        <option value="">Semua Bulan</option>
                        @foreach ($months as $mNum => $mName)
                            <option value="{{ $mNum }}" @selected($month == $mNum)>{{ $mName }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="flex-1 min-w-[160px]">
                    <x-select name="year" wrapperClass="w-full">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected($year == $y)>
                                {{ $y }} {{ $y == now()->year ? '(Tahun Berjalan)' : '' }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div class="flex-1 min-w-[190px]">
                    <x-select name="sort_dir" wrapperClass="w-full">
                        <option value="asc" @selected($sortDir === 'asc')>↑ Nomor Agenda (Menaik)</option>
                        <option value="desc" @selected($sortDir === 'desc')>↓ Nomor Agenda (Menurun)</option>
                    </x-select>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <x-select name="letter_type" wrapperClass="w-full">
                        <option value="">Semua Jenis (I s.d. X)</option>
                        @foreach ($letterTypes as $type)
                            <option value="{{ $type }}" @selected($letterType === $type)>Jenis {{ $type }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <x-button type="submit" variant="primary" class="w-full sm:w-auto">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <span>Filter</span>
                    </x-button>

                    <x-button variant="danger" href="{{ route('agenda-book.index') }}" title="Reset semua filter" class="w-full sm:w-auto">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        <span>Reset</span>
                    </x-button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($classifications as $cKey => $cMeta)
                @php
                    $count = $typeCounts[$cKey] ?? 0;
                @endphp
                <div @click="openAccordion('{{ $cKey }}')"
                     class="group relative cursor-pointer rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xs transition-all hover:-translate-y-0.5 hover:border-[#033F63] hover:shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold tracking-wider text-slate-500 group-hover:text-[#033F63]">
                            JENIS {{ $cKey }}
                        </span>
                        <span class="h-2 w-2 rounded-full {{ $cMeta['dot'] }}"></span>
                    </div>
                    <div class="mt-2 text-2xl font-black tracking-tight text-slate-900">
                        {{ number_format($count, 0, ',', '.') }}
                    </div>
                    <div class="mt-1 truncate text-[11px] font-medium text-slate-500">
                        {{ $cMeta['short'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="space-y-4">
            @forelse ($letters as $type => $group)
                @php
                    $meta = $classifications[$type] ?? [
                        'code' => $type,
                        'title' => "Jenis {$type} — Klasifikasi Khusus",
                        'short' => "Jenis {$type}",
                        'subtitle' => "Kode Klasifikasi: {$type}-001 s.d. {$type}-999 • Buku Registrasi Surat Masuk",
                        'dot' => 'bg-slate-500',
                    ];
                @endphp

                <div id="accordion-{{ $type }}"
                     class="overflow-hidden rounded-2xl border border-emerald-200/90 bg-white shadow-xs transition-colors"
                     x-data="{
                         page: 1,
                         perPage: 25,
                         get totalItems() {
                             return {{ $group->count() }};
                         },
                         get totalPages() {
                             return Math.ceil(this.totalItems / this.perPage) || 1;
                         },
                         get startItem() {
                             return (this.page - 1) * this.perPage + 1;
                         },
                         get endItem() {
                             return Math.min(this.page * this.perPage, this.totalItems);
                         }
                     }">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 bg-[#E8F7F0] px-5 py-3.5 border-b border-emerald-200/70 select-none">
                        <div class="flex items-center gap-3.5 cursor-pointer flex-1"
                             @click="toggleAccordion('{{ $type }}')">
                            <button type="button"
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-emerald-700 shadow-2xs transition-transform duration-200"
                                    :class="activeAccordion === '{{ $type }}' ? 'rotate-0' : 'rotate-180'">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="18 15 12 9 6 15"/>
                                </svg>
                            </button>

                            <div>
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h2 class="text-sm font-bold text-slate-900">
                                        {{ $meta['title'] }}
                                    </h2>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-100/80 text-cyan-800">
                                        {{ $group->count() }} Surat Terdaftar
                                    </span>
                                    @if ($month)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-100 text-emerald-800">
                                            Bulan {{ $months[$month] }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $meta['subtitle'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <div class="relative w-56 sm:w-64">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       x-model="searchTerms['{{ $type }}']"
                                       placeholder="Cari nomor, perihal, catatan..."
                                       class="w-full h-8.5 rounded-xl border border-slate-200 bg-white pl-8.5 pr-7 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:border-slate-300 focus:border-emerald-600 focus:outline-none focus:ring-1 focus:ring-emerald-600">
                                <button type="button"
                                        x-show="searchTerms['{{ $type }}']"
                                        @click="searchTerms['{{ $type }}'] = ''"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>

                            <button type="button"
                                    onclick="window.print()"
                                    class="inline-flex h-8.5 items-center gap-1.5 rounded-xl border border-emerald-300/80 bg-white px-3 text-xs font-semibold text-emerald-800 shadow-2xs transition hover:bg-emerald-50">
                                <svg class="h-3.5 w-3.5 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 6 2 18 2 18 9"/>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                    <rect x="6" y="14" width="12" height="8"/>
                                </svg>
                                <span>Cetak Bagian Ini</span>
                            </button>
                        </div>
                    </div>

                    <div x-show="activeAccordion === '{{ $type }}'"
                         x-collapse
                         class="border-t border-slate-100">
                        <div class="flex items-center justify-between bg-slate-50/70 px-5 py-2.5 text-xs text-slate-500 border-b border-slate-100">
                            <div class="flex items-center gap-2 font-medium">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                                </svg>
                                <span>Diurutkan berdasarkan nomor agenda ({{ $sortDir === 'desc' ? 'menurun' : 'menaik' }})</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="text-slate-600 font-semibold" x-text="viewMode === 'detailed' ? 'Mode Matriks Register Lengkap' : 'Mode Ringkas'"></span>
                            </div>
                            <div>
                                Menampilkan <span x-text="startItem">1</span>-<span x-text="endItem">{{ min(25, $group->count()) }}</span> dari <strong class="text-slate-700">{{ $group->count() }}</strong> nomor agenda
                            </div>
                        </div>

                        <div class="overflow-x-auto" x-show="viewMode === 'detailed'">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-700 border-b border-slate-300">
                                        <th rowspan="2" class="py-2.5 px-3 text-center border-r border-slate-200 w-12">NO.</th>
                                        <th rowspan="2" class="py-2.5 px-3 text-center border-r border-slate-200 whitespace-nowrap min-w-[100px]">NO. AGENDA</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 whitespace-nowrap min-w-[95px]">TGL. SURAT</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 min-w-[220px]">PENGIRIMAN &amp; NO. SURAT</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 min-w-[260px]">ISI RINGKAS (PERIHAL)</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 whitespace-nowrap min-w-[95px]">TGL. TERIMA</th>
                                        <th colspan="6" class="py-1.5 px-2 text-center border-r border-slate-200 bg-slate-200/70 text-slate-800">
                                            KETERANGAN DIREKSI / PIMPINAN
                                        </th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 whitespace-nowrap min-w-[110px]">KE BAHAGIAN</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 whitespace-nowrap min-w-[130px]">DISPOSISI</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 min-w-[280px]">CATATAN DISPOSISI</th>
                                        <th rowspan="2" class="py-2.5 px-3 border-r border-slate-200 whitespace-nowrap min-w-[100px]">PENYIMPANAN</th>
                                        <th rowspan="2" class="py-2.5 px-3 text-center min-w-[80px]">AKSI</th>
                                    </tr>
                                    <tr class="bg-slate-50 text-[10px] font-bold text-slate-600 border-b border-slate-200">
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-12" title="Direktur Utama / Region Head">DIRUT</th>
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-14" title="Direktur Produksi / Operation Head I">DIRPROD</th>
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-12" title="Direktur Keuangan / Business Support Head">DIRKEU</th>
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-16" title="Direktur Renbang / Operation Head II">DIRRENBANG</th>
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-12" title="Direktur SDM">DIRSDM</th>
                                        <th class="py-1.5 px-1.5 text-center border-r border-slate-200 w-14" title="Kepala Bagian / KA.3.00">KA.3.00</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @foreach ($group as $index => $letter)
                                        @php
                                            // Kumpulan kode & abbr direktur untuk checklist kolom Keterangan
                                            $letterDirectorCodes = collect([$letter->director?->code])
                                                ->merge($letter->directorRecipients->pluck('code'))
                                                ->filter()
                                                ->unique()
                                                ->all();

                                            $letterDirectorAbbrs = collect([$letter->director?->abbr])
                                                ->merge($letter->directorRecipients->pluck('abbr'))
                                                ->filter()
                                                ->unique()
                                                ->all();

                                            // Evaluasi 6 Direksi Matrix
                                            $hasDirut = (bool) array_intersect($letterDirectorCodes, ['1', '11', '12']) || (bool) array_intersect($letterDirectorAbbrs, ['DIRUT', 'WADIRUT', 'DIRPEL', 'DIROPS']);
                                            $hasDirprod = (bool) array_intersect($letterDirectorCodes, ['2', '21', '22']) || (bool) array_intersect($letterDirectorAbbrs, ['DIRPROD', 'DIRPEM', 'SEVPPROD']);
                                            $hasDirkeu = (bool) array_intersect($letterDirectorCodes, ['3', '31']) || (bool) array_intersect($letterDirectorAbbrs, ['DIRKEU', 'SEVPKEU']);
                                            $hasDirrenbang = (bool) array_intersect($letterDirectorCodes, ['5', '51', '41']) || (bool) array_intersect($letterDirectorAbbrs, ['DIRRENBANG', 'DIRRENBANG & PEM', 'SEVPSDM']);
                                            $hasDirsdm = in_array('4', $letterDirectorCodes, true) || in_array('DIRSDM', $letterDirectorAbbrs, true);
                                            $hasKabag = in_array('6', $letterDirectorCodes, true) || in_array('KABAG 3.00', $letterDirectorAbbrs, true) || in_array('KA.3.00', $letterDirectorAbbrs, true);

                                            // Bahagian tujuan
                                            $departments = $letter->departmentRecipients;

                                            // Disposisi unik
                                            $dispositionLabels = $letter->dispositions
                                                ->map(fn($d) => $d->dispositionType?->label)
                                                ->filter()
                                                ->unique()
                                                ->values();

                                            // Derajat Surat
                                            $derajat = $letter->status ?? 'Biasa';
                                            $derajatClass = match ($derajat) {
                                                'Sangat Segera' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                'Segera' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'Rahasia' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            };

                                            // Search blob komprehensif
                                            $searchBlob = strtolower(implode(' ', [
                                                $letter->letter_no ?? '',
                                                $letter->subject ?? '',
                                                $letter->content ?? '',
                                                $letter->sender_name ?? '',
                                                $letter->senderUnit?->name ?? '',
                                                $letter->agenda_no ?? '',
                                                $letter->location ?? '',
                                                $departments->pluck('code')->join(' '),
                                                $departments->pluck('name')->join(' '),
                                                $dispositionLabels->join(' '),
                                                "{$type}-{$letter->agenda_no}",
                                                "{$type}/{$letter->agenda_no}",
                                            ]));
                                        @endphp

                                        <tr class="transition hover:bg-emerald-50/30 align-top"
                                            x-show="matches('{{ addslashes($searchBlob) }}', searchTerms['{{ $type }}']) && (searchTerms['{{ $type }}'] || ({{ $index }} >= (page - 1) * perPage && {{ $index }} < page * perPage))">
                                            <td class="py-3 px-3 text-center border-r border-slate-200 text-slate-500 font-mono text-[11px]">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200 text-center whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black font-mono bg-sky-50 text-[#033F63] border border-sky-200">
                                                    {{ $letter->letter_type }} / {{ $letter->agenda_no }}{{ $letter->agenda_series ? '-'.$letter->agenda_series : '' }}
                                                </span>
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200 whitespace-nowrap text-slate-700 font-medium">
                                                {{ $letter->letter_date?->format('d/m/Y') ?? '-' }}
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200">
                                                <div class="font-bold text-slate-900 leading-tight">
                                                    {{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}
                                                </div>
                                                <div class="mt-1">
                                                    <a href="{{ route('letters.show', $letter) }}"
                                                       class="font-mono text-[11.5px] font-semibold text-[#033F63] hover:underline"
                                                       title="Lihat detail surat">
                                                        {{ $letter->letter_no }}
                                                    </a>
                                                </div>
                                                <div class="mt-1 text-[10.5px] text-slate-500">
                                                    Banyaknya:
                                                    <span class="font-semibold text-slate-700">
                                                        @if ($letter->pages)
                                                            {{ $letter->pages }} Lembar
                                                        @elseif ($letter->attachment_path)
                                                            1 Berkas
                                                        @else
                                                            -
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200">
                                                <div class="font-medium text-slate-900 leading-snug">
                                                    {{ $letter->subject }}
                                                </div>
                                                <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $derajatClass }}">
                                                        {{ $derajat }}
                                                    </span>
                                                    @if ($letter->director)
                                                        <span class="text-[10.5px] text-slate-500">
                                                            Kepada: <strong class="text-slate-700">{{ $letter->director->abbr ?? $letter->director->name }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200 whitespace-nowrap">
                                                <div class="font-semibold text-slate-900">
                                                    {{ $letter->received_date?->format('d/m/Y') ?? '-' }}
                                                </div>
                                                <div class="text-[10.5px] text-slate-400 mt-0.5">
                                                    {{ $letter->created_at?->format('H:i') ?? '08:00' }} WIB
                                                </div>
                                            </td>

                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasDirut ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasDirut)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: DIRUT / Region Head">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasDirprod ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasDirprod)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: DIRPROD / Ops Head I">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasDirkeu ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasDirkeu)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: DIRKEU / Business Support">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasDirrenbang ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasDirrenbang)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: DIRRENBANG / Ops Head II">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasDirsdm ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasDirsdm)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: DIRSDM">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-1 text-center border-r border-slate-200 {{ $hasKabag ? 'bg-emerald-50/60' : '' }}">
                                                @if ($hasKabag)
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs" title="Tujuan: KABAG 3.00">✓</span>
                                                @else
                                                    <span class="text-slate-300">-</span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200">
                                                @if ($departments->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach ($departments as $dept)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-300/80"
                                                                  title="{{ $dept->name }}">
                                                                {{ $dept->code }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200">
                                                @if ($dispositionLabels->isNotEmpty())
                                                    <div class="flex flex-col gap-1">
                                                        @foreach ($dispositionLabels as $dLabel)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-teal-50 text-teal-800 border border-teal-200 w-fit">
                                                                {{ $dLabel }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                        Belum Disposisi
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200">
                                                @if (!empty($letter->content))
                                                    <div class="rounded-lg bg-slate-50 p-2 border border-slate-200/80 font-mono text-[11px] text-slate-800 leading-relaxed whitespace-pre-line"
                                                         x-data="{ expanded: false }">
                                                        <div :class="expanded ? '' : 'line-clamp-3'">
                                                            {!! nl2br(e($letter->content)) !!}
                                                        </div>
                                                        @if (strlen($letter->content) > 120)
                                                            <button type="button"
                                                                    @click="expanded = !expanded"
                                                                    class="mt-1 text-[10px] font-bold text-[#033F63] hover:underline"
                                                                    x-text="expanded ? 'Tutup' : 'Lihat Selengkapnya...'">
                                                            </button>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                                @endif
                                            </td>

                                            <td class="py-3 px-3 border-r border-slate-200 text-slate-600 font-medium whitespace-nowrap">
                                                {{ $letter->location ?? '-' }}
                                            </td>

                                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <a href="{{ route('letters.show', $letter) }}"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition shadow-2xs"
                                                       title="Lihat Detail Surat">
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('letters.print', $letter) }}"
                                                       target="_blank"
                                                       class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition shadow-2xs"
                                                       title="Cetak Lembar Disposisi (4 Hal)">
                                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polyline points="6 9 6 2 18 2 18 9"/>
                                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                                            <rect x="6" y="14" width="12" height="8"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="overflow-x-auto" x-show="viewMode === 'compact'">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-600 border-b border-slate-200">
                                        <th class="py-3.5 pl-5 pr-3">NO. AGENDA</th>
                                        <th class="py-3.5 px-3">NO. SURAT</th>
                                        <th class="py-3.5 px-3 min-w-[280px]">PERIHAL &amp; DERAJAT</th>
                                        <th class="py-3.5 px-3 min-w-[200px]">PENGIRIM</th>
                                        <th class="py-3.5 px-3 whitespace-nowrap">TGL TERIMA</th>
                                        <th class="py-3.5 px-3 whitespace-nowrap">STATUS DISPOSISI</th>
                                        <th class="py-3.5 pl-3 pr-5 text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($group as $index => $letter)
                                        @php
                                            $searchBlob = strtolower(implode(' ', [
                                                $letter->letter_no ?? '',
                                                $letter->subject ?? '',
                                                $letter->sender_name ?? '',
                                                $letter->senderUnit?->name ?? '',
                                                $letter->agenda_no ?? '',
                                                $letter->content ?? '',
                                                "{$type}-{$letter->agenda_no}"
                                            ]));

                                            if ($letter->follow_up) {
                                                $dispBadge = ['label' => 'Selesai / Terarsip', 'class' => 'bg-emerald-100/80 text-emerald-800', 'dot' => 'bg-emerald-500'];
                                            } elseif (($letter->dispositions_count ?? 0) > 0) {
                                                $dispBadge = ['label' => 'Sudah Disposisi', 'class' => 'bg-cyan-100/80 text-cyan-800', 'dot' => 'bg-cyan-500'];
                                            } else {
                                                $dispBadge = ['label' => 'Belum Disposisi', 'class' => 'bg-amber-100/80 text-amber-800', 'dot' => 'bg-amber-500'];
                                            }

                                            $derajat = $letter->status ?? 'Biasa';
                                            $derajatClass = match ($derajat) {
                                                'Sangat Segera' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                'Segera' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'Rahasia' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                default => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            };
                                        @endphp
                                        <tr class="transition hover:bg-slate-50/80"
                                            x-show="matches('{{ addslashes($searchBlob) }}', searchTerms['{{ $type }}']) && (searchTerms['{{ $type }}'] || ({{ $index }} >= (page - 1) * perPage && {{ $index }} < page * perPage))">
                                            <td class="py-3.5 pl-5 pr-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#E0F2FE] text-[#033F63]">
                                                    {{ $letter->letter_type }}-{{ Str::padLeft($letter->agenda_no, 3, '0') }}{{ $letter->agenda_series ? '-'.$letter->agenda_series : '' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-3 font-bold text-slate-900 whitespace-nowrap">
                                                <a href="{{ route('letters.show', $letter) }}" class="hover:text-[#033F63] hover:underline">
                                                    {{ $letter->letter_no }}
                                                </a>
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="font-medium text-slate-900 leading-snug line-clamp-2">
                                                    {{ $letter->subject }}
                                                </div>
                                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $derajatClass }}">
                                                        {{ $derajat }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="font-bold text-slate-900">
                                                    {{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 mt-0.5 truncate">
                                                    {{ $letter->director?->abbr ?? $letter->director?->name ?? 'Tujuan: Direksi' }}
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3 whitespace-nowrap">
                                                <div class="font-medium text-slate-900">
                                                    {{ $letter->received_date?->translatedFormat('d M Y') ?? '-' }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">
                                                    {{ $letter->created_at?->format('H:i') ?? '08:00' }} WIB
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3 whitespace-nowrap">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $dispBadge['class'] }}">
                                                    <span class="h-1.5 w-1.5 rounded-full {{ $dispBadge['dot'] }}"></span>
                                                    <span>{{ $dispBadge['label'] }}</span>
                                                </span>
                                            </td>
                                            <td class="py-3.5 pl-3 pr-5 text-center whitespace-nowrap">
                                                <a href="{{ route('letters.show', $letter) }}"
                                                   class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition shadow-2xs">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70 px-5 py-3 text-xs text-slate-500 border-t border-slate-200">
                            <div>
                                Menampilkan <span x-text="startItem">1</span> - <span x-text="endItem">{{ min(25, $group->count()) }}</span> dari <strong class="text-slate-800">{{ $group->count() }}</strong> nomor agenda
                            </div>

                            <div class="flex items-center gap-2">
                                <select x-model.number="perPage"
                                        @change="page = 1"
                                        class="h-8 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-medium text-slate-700 shadow-2xs focus:border-[#033F63] focus:outline-none no-select2">
                                    <option value="10">10 baris per halaman</option>
                                    <option value="25" selected>25 baris per halaman</option>
                                    <option value="50">50 baris per halaman</option>
                                    <option value="100">100 baris per halaman</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-1" x-show="totalPages > 1">
                                <button type="button"
                                        @click="if (page > 1) page--"
                                        :disabled="page === 1"
                                        class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 transition hover:bg-slate-100 disabled:opacity-40 disabled:pointer-events-none">
                                    Sebelumnya
                                </button>

                                <template x-for="p in totalPages" :key="p">
                                    <button type="button"
                                            x-show="p === 1 || p === totalPages || (p >= page - 1 && p <= page + 1)"
                                            @click="page = p"
                                            :class="page === p ? 'bg-[#033F63] text-white border-[#033F63]' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
                                            class="h-8 w-8 rounded-lg border text-xs font-bold transition flex items-center justify-center"
                                            x-text="p">
                                    </button>
                                </template>

                                <button type="button"
                                        @click="if (page < totalPages) page++"
                                        :disabled="page === totalPages"
                                        class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-600 transition hover:bg-slate-100 disabled:opacity-40 disabled:pointer-events-none">
                                    Selanjutnya
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-xs">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800">Tidak ada data register agenda</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Tidak ditemukan nomor register surat masuk untuk
                        @if ($month)
                            bulan {{ $months[$month] }}
                        @endif
                        tahun {{ $year }}
                        @if ($letterType)
                            dengan filter Jenis {{ $letterType }}
                        @endif.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
