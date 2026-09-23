@extends('layouts.app')

@section('title', $letter ? 'Edit Surat Masuk' : 'Input Surat Masuk')
@section('breadcrumb', 'Arsip Surat / ' . ($letter ? 'Edit' : 'Input Surat'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6 pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ $letter ? route('letters.show', $letter) : route('letters.index') }}"
                   class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-2xs transition hover:bg-slate-50 hover:text-slate-800 hover:border-slate-300"
                   title="Kembali ke arsip">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900">
                            {{ $letter ? 'Edit Surat Masuk' : 'Input Surat Masuk' }}
                        </h1>
                        @if ($letter)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-[#033F63]/10 text-[#033F63]">
                                {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $letter ? "Nomor Surat: {$letter->letter_no}" : 'Lengkapi formulir registrasi surat masuk baru' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <x-button variant="secondary" href="{{ $letter ? route('letters.show', $letter) : route('letters.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" form="letter-form" variant="primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Simpan</span>
                </x-button>
            </div>
        </div>

        {{-- Error Summary Alert --}}
        @if (isset($errors) && $errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-800 shadow-xs">
                <div class="flex items-center gap-2 font-bold mb-1.5 text-rose-900">
                    <svg class="h-4 w-4 shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="letter-form"
              method="POST"
              action="{{ $letter ? route('letters.update', $letter) : route('letters.store') }}"
              enctype="multipart/form-data"
              class="space-y-6"
              x-data="{
                  senderMode: '{{ old('sender_unit_id', $letter?->sender_unit_id) ? 'unit' : (old('sender_name', $letter?->sender_name) ? 'manual' : 'unit') }}'
              }">
            @csrf
            @if ($letter)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Informasi Surat Masuk</h2>
                        <p class="text-[11px] text-slate-400">Identitas nomor agenda, tanggal, dan perihal resmi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Jenis Surat --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jenis Surat / Agenda <span class="text-rose-500">*</span>
                        </label>
                        @if ($letter)
                            <div class="w-full h-10 flex items-center rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 text-xs font-bold text-slate-600 select-none cursor-not-allowed">
                                {{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Jenis &amp; nomor agenda dikunci dan tidak bisa diubah.</p>
                        @else
                            <x-select name="letter_type" required wrapperClass="w-full">
                                <option value="">-- Pilih Jenis Agenda --</option>
                                @foreach (['I', 'II', 'III', 'IV', 'V'] as $type)
                                    <option value="{{ $type }}" @selected(old('letter_type') === $type)>Jenis {{ $type }}</option>
                                @endforeach
                            </x-select>
                            <p class="mt-1 text-[11px] text-slate-400">Nomor agenda diterbitkan berurutan otomatis saat disimpan.</p>
                        @endif
                    </div>

                    {{-- Seri Agenda --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Seri Agenda
                        </label>
                        <input type="text" name="agenda_series" maxlength="10"
                               value="{{ old('agenda_series', $letter?->agenda_series) }}"
                               placeholder="Contoh: A, B, dst"
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs uppercase text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Nomor Surat --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor Surat <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="letter_no" required maxlength="50"
                               value="{{ old('letter_no', $letter?->letter_no) }}"
                               placeholder="Nomor surat resmi pengirim..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Jumlah Lembar --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jumlah Lembar
                        </label>
                        <input type="number" name="pages" min="0"
                               value="{{ old('pages', $letter?->pages) }}"
                               placeholder="0"
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Tanggal Surat --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Surat
                        </label>
                        <x-datepicker name="letter_date" :value="old('letter_date', $letter?->letter_date?->format('Y-m-d'))" placeholder="Pilih tanggal surat..." />
                    </div>

                    {{-- Tanggal Terima --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Terima
                        </label>
                        <x-datepicker name="received_date" :value="old('received_date', $letter?->received_date?->format('Y-m-d'))" placeholder="Pilih tanggal terima..." />
                        @unless ($letter)
                            <p class="mt-1 text-[11px] text-slate-400">Menentukan tahun agenda. Kosongkan jika sama dengan Tanggal Surat.</p>
                        @endunless
                    </div>

                    {{-- Lokasi Penyimpanan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Lokasi Penyimpanan Fisik (Bantex/Ordner)
                        </label>
                        <input type="text" name="location" maxlength="50"
                               value="{{ old('location', $letter?->location) }}"
                               placeholder="Contoh: ORDNER-2026-A"
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs uppercase text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Kata Kunci --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kata Kunci (Keyword)
                        </label>
                        <input type="text" name="keyword" maxlength="255"
                               value="{{ old('keyword', $letter?->keyword) }}"
                               placeholder="Kata kunci pencarian..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Hal / Perihal --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Perihal (Hal) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="subject" required rows="2" maxlength="300"
                                  placeholder="Ketik ringkasan isi atau perihal surat..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">{{ old('subject', $letter?->subject) }}</textarea>
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan Khusus
                        </label>
                        <textarea name="content" rows="3"
                                  placeholder="Catatan penerimaan, ringkasan disposisi verbal, dll..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">{{ old('content', $letter?->content) }}</textarea>
                    </div>

                    {{-- Lampiran Berkas --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Berkas Lampiran Digital
                        </label>
                        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-3.5 transition hover:border-[#033F63]/40 hover:bg-slate-50">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.48-8.48"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <input type="file" name="attachment" id="attachment"
                                               class="text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-[#033F63]/10 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-[#033F63] hover:file:bg-[#033F63]/20 cursor-pointer">
                                        <p class="mt-0.5 text-[11px] text-slate-400">PDF, DOCX, JPG, PNG (Maksimal 10 MB)</p>
                                    </div>
                                </div>
                                @if ($letter?->attachment_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($letter->attachment_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-sky-200 bg-sky-50 text-xs font-semibold text-sky-700 hover:bg-sky-100 transition">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <span>Lihat Berkas Saat Ini</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Asal Pengirim</h2>
                        <p class="text-[11px] text-slate-400">Pilih dari daftar master unit kerja atau masukkan manual</p>
                    </div>
                </div>

                {{-- Mode Pengirim: Pill Switcher --}}
                <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/80 mb-3">
                    <button type="button" @click="senderMode = 'unit'; $nextTick(() => { if (window.initSelect2) window.initSelect2($el.closest('form')); })"
                            :class="senderMode === 'unit' ? 'bg-white text-[#033F63] font-bold shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        Pilih dari Master Unit
                    </button>
                    <button type="button" @click="senderMode = 'manual'"
                            :class="senderMode === 'manual' ? 'bg-white text-[#033F63] font-bold shadow-2xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3.5 py-1.5 rounded-lg text-xs transition cursor-pointer">
                        Isi Manual (Lain-lain)
                    </button>
                </div>

                {{-- Pilihan Master Unit --}}
                <div x-show="senderMode === 'unit'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Unit Pengirim Master
                    </label>
                    <x-select name="sender_unit_id" wrapperClass="w-full">
                        <option value="">-- Pilih Unit Pengirim Terdaftar --</option>
                        @foreach ($senderUnits as $unit)
                            <option value="{{ $unit->id }}" @selected(old('sender_unit_id', $letter?->sender_unit_id) == $unit->id)>
                                {{ $unit->name }} @if ($unit->abbr) ({{ $unit->abbr }}) @endif
                            </option>
                        @endforeach
                    </x-select>
                </div>

                {{-- Input Manual Pengirim --}}
                <div x-show="senderMode === 'manual'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama Instansi / Pengirim Manual
                    </label>
                    <input type="text" name="sender_name" maxlength="255"
                           value="{{ old('sender_name', $letter?->sender_name) }}"
                           placeholder="Ketik nama instansi, kementerian, vendor, atau perorangan..."
                           class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                </div>
            </div>

            @if ($letter)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs">
                    <div class="flex items-center gap-3 pb-4 mb-4 border-b border-slate-100">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Status Tindak Lanjut</h2>
                            <p class="text-[11px] text-slate-400">Progres penyelesaian disposisi surat masuk</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="relative flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer transition hover:bg-white hover:border-slate-300 select-none has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50/50 has-[:checked]:ring-1 has-[:checked]:ring-amber-400">
                            <input type="radio" name="follow_up" value="0" @checked(! old('follow_up', $letter->follow_up)) class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500 shrink-0"></span>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Belum Ditindaklanjuti</div>
                                    <div class="text-[11px] text-slate-400">Surat masih membutuhkan proses/tindak lanjut</div>
                                </div>
                            </div>
                        </label>
                        <label class="relative flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer transition hover:bg-white hover:border-slate-300 select-none has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-500">
                            <input type="radio" name="follow_up" value="1" @checked(old('follow_up', $letter->follow_up)) class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <div class="text-xs font-bold text-slate-800">Sudah Ditindaklanjuti</div>
                                    <div class="text-[11px] text-slate-400">Seluruh instruksi disposisi telah diselesaikan</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs">
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Tujuan &amp; Tembusan</h2>
                        <p class="text-[11px] text-slate-400">Instruksi disposisi spesifik diisi terpisah oleh Sekretaris Direksi/Kepala Bagian</p>
                    </div>
                </div>

                {{-- Tujuan Utama (Direktur) --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kepada (Direktur Utama Tujuan)
                    </label>
                    <x-select name="director_id" wrapperClass="w-full sm:max-w-md">
                        <option value="">-- Pilih Direktur Utama Tujuan --</option>
                        @foreach ($directors as $director)
                            <option value="{{ $director->id }}" @selected(old('director_id', $letter?->director_id) == $director->id)>
                                {{ $director->name }} ({{ $director->abbr ?: $director->code }})
                            </option>
                        @endforeach
                    </x-select>
                </div>

                @php
                    $selectedDirectors = old('director_recipients', $letter?->directorRecipients->pluck('id')->all() ?? []);
                    $selectedDepartments = old('department_recipients', $letter?->departmentRecipients->pluck('id')->all() ?? []);
                @endphp

                {{-- Tembusan Direksi Lain --}}
                <div class="mb-6 pt-5 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <label class="block text-xs font-semibold text-slate-700">
                            Tembusan Direksi Lain
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih satu atau lebih</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                        @foreach ($directors as $director)
                            <label class="group relative flex items-start gap-2.5 p-2.5 rounded-xl border border-slate-200/90 bg-slate-50/60 hover:bg-white hover:border-slate-300 cursor-pointer transition select-none has-[:checked]:border-[#033F63] has-[:checked]:bg-[#033F63]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#033F63]">
                                <input type="checkbox" name="director_recipients[]" value="{{ $director->id }}"
                                       @checked(in_array($director->id, $selectedDirectors))
                                       class="mt-0.5 h-3.5 w-3.5 rounded border-slate-300 text-[#033F63] focus:ring-[#033F63]">
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-slate-800 group-has-[:checked]:text-[#033F63] truncate">
                                        {{ $director->abbr ?: $director->code }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 group-has-[:checked]:text-slate-600 truncate" title="{{ $director->name }}">
                                        {{ $director->name }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Tembusan Bagian / Biro --}}
                <div class="pt-5 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <label class="block text-xs font-semibold text-slate-700">
                            Tembusan Bagian / Biro
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih bagian yang menerima tembusan</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach ($departments as $department)
                            <label class="group relative flex items-center gap-2 p-2 rounded-xl border border-slate-200/90 bg-slate-50/60 hover:bg-white hover:border-slate-300 cursor-pointer transition select-none has-[:checked]:border-[#033F63] has-[:checked]:bg-[#033F63]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#033F63]">
                                <input type="checkbox" name="department_recipients[]" value="{{ $department->id }}"
                                       @checked(in_array($department->id, $selectedDepartments))
                                       class="h-3.5 w-3.5 rounded border-slate-300 text-[#033F63] focus:ring-[#033F63]">
                                <span class="text-xs font-medium text-slate-700 group-has-[:checked]:text-[#033F63] group-has-[:checked]:font-bold truncate">
                                    {{ $department->code }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <x-button variant="secondary" href="{{ $letter ? route('letters.show', $letter) : route('letters.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>{{ $letter ? 'Simpan Perubahan' : 'Simpan Surat' }}</span>
                </x-button>
            </div>
        </form>
    </div>
@endsection
