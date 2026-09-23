@extends('layouts.app')

@section('title', $division ? 'Edit Surat Bagian' : 'Input Surat Bagian')
@section('breadcrumb', 'Surat Bagian / ' . ($division ? 'Edit' : 'Input Surat'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6 pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ $division ? route('letter-divisions.show', $division) : route('letter-divisions.index') }}"
                   class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-2xs transition hover:bg-slate-50 hover:text-slate-800 hover:border-slate-300"
                   title="Kembali ke daftar surat bagian">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900">
                            {{ $division ? 'Edit Surat Bagian' : 'Input Surat Bagian Baru' }}
                        </h1>
                        @if ($division)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-[#033F63]/10 text-[#033F63]">
                                {{ $division->agenda_no }}/{{ $division->agenda_type_code }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $division ? "Nomor Surat: {$division->letter_no}" : 'Lengkapi formulir agenda surat masuk bagian' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <x-button variant="secondary" href="{{ $division ? route('letter-divisions.show', $division) : route('letter-divisions.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" form="division-form" variant="primary">
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

        <form id="division-form"
              method="POST"
              action="{{ $division ? route('letter-divisions.update', $division) : route('letter-divisions.store') }}"
              class="space-y-6">
            @csrf
            @if ($division)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
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
                        <h2 class="text-sm font-bold text-slate-900">Informasi Agenda &amp; Surat</h2>
                        <p class="text-[11px] text-slate-400">Identitas nomor agenda bagian, nomor surat, dan tanggal</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Kode Tipe Agenda --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kode Tipe Agenda <span class="text-rose-500">*</span>
                        </label>
                        @if ($division)
                            <div class="w-full h-10 flex items-center rounded-xl border border-slate-200 bg-slate-100/80 px-3.5 text-xs font-bold text-slate-600 select-none cursor-not-allowed">
                                {{ $division->agenda_no }}/{{ $division->agenda_type_code }}
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Nomor agenda bagian dikunci dan tidak bisa diubah.</p>
                        @else
                            <input type="text" name="agenda_type_code" required maxlength="20"
                                   value="{{ old('agenda_type_code') }}"
                                   placeholder="Contoh: SP-III, SP-1..."
                                   class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs uppercase font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                            <p class="mt-1 text-[11px] text-slate-400">Nomor agenda diterbitkan berurutan otomatis saat disimpan.</p>
                        @endif
                    </div>

                    {{-- Jenis Bagian --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jenis Surat
                        </label>
                        <input type="text" name="division_type" maxlength="10"
                               value="{{ old('division_type', $division?->division_type) }}"
                               placeholder="Contoh: Dinas, Nota..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Nomor Surat --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nomor Surat
                        </label>
                        <input type="text" name="letter_no" maxlength="100"
                               value="{{ old('letter_no', $division?->letter_no) }}"
                               placeholder="Nomor surat asal pengirim..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Status Klasifikasi --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Status Klasifikasi
                        </label>
                        <x-select name="status" wrapperClass="w-full">
                            <option value="">-- Pilih Status --</option>
                            @foreach (['Sangat Segera', 'Segera', 'Rahasia', 'Biasa'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $division?->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Tanggal Agenda --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Agenda
                        </label>
                        <x-datepicker name="agenda_date"
                                      :value="old('agenda_date', $division?->agenda_date?->format('Y-m-d'))"
                                      placeholder="Pilih tanggal agenda..." />
                        @unless ($division)
                            <p class="mt-1 text-[11px] text-slate-400">Menentukan tahun nomor agenda. Kosongkan untuk pakai Tgl Terima.</p>
                        @endunless
                    </div>

                    {{-- Tanggal Surat --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Surat
                        </label>
                        <x-datepicker name="letter_date"
                                      :value="old('letter_date', $division?->letter_date?->format('Y-m-d'))"
                                      placeholder="Pilih tanggal surat..." />
                    </div>

                    {{-- Tanggal Terima --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Diterima
                        </label>
                        <x-datepicker name="received_date"
                                      :value="old('received_date', $division?->received_date?->format('Y-m-d'))"
                                      placeholder="Pilih tanggal diterima..." />
                    </div>

                    {{-- Jumlah Lembar --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Jumlah Lembar
                        </label>
                        <input type="number" step="0.01" name="amount" min="0"
                               value="{{ old('amount', $division?->amount) }}"
                               placeholder="Jumlah lembar atau berkas..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Pengirim, Tujuan &amp; Isi Surat</h2>
                        <p class="text-[11px] text-slate-400">Pihak yang berkomunikasi serta uraian perihal surat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Dari / Pengirim --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Dari (Asal Pengirim)
                        </label>
                        <input type="text" name="sender_name" maxlength="255"
                               value="{{ old('sender_name', $division?->sender_name) }}"
                               placeholder="Pengirim atau instansi pembuat surat..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Urusan --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Urusan / Bidang
                        </label>
                        <input type="text" name="matter" maxlength="255"
                               value="{{ old('matter', $division?->matter) }}"
                               placeholder="Bidang atau konteks urusan surat..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Kepada / Tujuan --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kepada (Tujuan Penerima)
                        </label>
                        <textarea name="addressee" rows="2"
                                  placeholder="Pejabat atau unit tujuan penerima surat..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">{{ old('addressee', $division?->addressee) }}</textarea>
                    </div>

                    {{-- Perihal / Hal --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Perihal (Hal) <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="subject" required rows="2.5"
                                  placeholder="Tuliskan pokok perihal atau inti surat secara jelas..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">{{ old('subject', $division?->subject) }}</textarea>
                    </div>

                    {{-- Catatan Khusus --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Catatan Khusus
                        </label>
                        <textarea name="note" rows="2"
                                  placeholder="Catatan tambahan, ringkasan disposisi, atau keterangan lain..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">{{ old('note', $division?->note) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                            <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Tembusan Direksi &amp; SEVP</h2>
                            <p class="text-[11px] text-slate-400">Pilih satu atau lebih pejabat penerima tembusan</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400">Pilih satu atau lebih</span>
                </div>

                @php
                    $selectedDirectors = old('director_recipients', $division?->directorRecipients->pluck('id')->all() ?? []);
                @endphp

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

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" href="{{ $division ? route('letter-divisions.show', $division) : route('letter-divisions.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>{{ $division ? 'Simpan Perubahan' : 'Simpan Surat Bagian' }}</span>
                </x-button>
            </div>
        </form>
    </div>
@endsection
