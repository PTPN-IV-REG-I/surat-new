@extends('layouts.app')

@section('title', $letter ? 'Edit Surat' : 'Input Surat')
@section('breadcrumb', 'Arsip Surat / ' . ($letter ? 'Edit' : 'Input Surat'))

@section('content')
    <div class="mx-auto max-w-3xl space-y-4">
        <h1 class="text-lg font-semibold text-slate-900">{{ $letter ? "Edit Surat: {$letter->letter_no}" : 'Input Surat Masuk' }}</h1>

        <form method="POST"
              action="{{ $letter ? route('letters.update', $letter) : route('letters.store') }}"
              enctype="multipart/form-data"
              class="space-y-6"
              x-data="{ senderMode: '{{ old('sender_unit_id', $letter?->sender_unit_id) ? 'unit' : 'manual' }}' }">
            @csrf
            @if ($letter)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Info Surat</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jenis Surat</label>
                        @if ($letter)
                            <input type="text" value="{{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}" disabled
                                   class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Jenis &amp; nomor agenda tidak bisa diubah setelah surat dibuat.</p>
                        @else
                            <select name="letter_type" required
                                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                <option value="">-- pilih --</option>
                                @foreach (['I', 'II', 'III', 'IV', 'V'] as $type)
                                    <option value="{{ $type }}" @selected(old('letter_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-400">No. Agenda dibuat otomatis saat surat disimpan.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Seri</label>
                        <input type="text" name="agenda_series" maxlength="10"
                               value="{{ old('agenda_series', $letter?->agenda_series) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">No. Surat</label>
                        <input type="text" name="letter_no" required maxlength="50"
                               value="{{ old('letter_no', $letter?->letter_no) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jumlah Lembar</label>
                        <input type="number" name="pages" min="0"
                               value="{{ old('pages', $letter?->pages) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tgl Surat</label>
                        <input type="date" name="letter_date"
                               value="{{ old('letter_date', $letter?->letter_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tgl Terima</label>
                        <input type="date" name="received_date"
                               value="{{ old('received_date', $letter?->received_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        @unless ($letter)
                            <p class="mt-1 text-xs text-slate-400">Menentukan tahun No. Agenda. Kosongkan untuk pakai Tgl Surat.</p>
                        @endunless
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Lokasi Penyimpanan</label>
                        <input type="text" name="location" maxlength="50"
                               value="{{ old('location', $letter?->location) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Kata Kunci</label>
                        <input type="text" name="keyword" maxlength="255"
                               value="{{ old('keyword', $letter?->keyword) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Hal</label>
                        <textarea name="subject" required rows="2" maxlength="300"
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('subject', $letter?->subject) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea name="content" rows="3"
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('content', $letter?->content) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Lampiran</label>
                        <input type="file" name="attachment"
                               class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                        @if ($letter?->attachment_path)
                            <p class="mt-1 text-xs text-slate-500">
                                Lampiran saat ini: <a href="{{ \Illuminate\Support\Facades\Storage::url($letter->attachment_path) }}" target="_blank" class="text-emerald-600 hover:underline">lihat</a>
                                (upload baru akan menggantikan)
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Pengirim</h2>

                <div class="flex gap-4 text-sm">
                    <label class="flex items-center gap-2">
                        <input type="radio" x-model="senderMode" value="unit" class="text-emerald-600 focus:ring-emerald-500">
                        Pilih dari master
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" x-model="senderMode" value="manual" class="text-emerald-600 focus:ring-emerald-500">
                        Isi manual (Lain-lain)
                    </label>
                </div>

                <div x-show="senderMode === 'unit'" x-cloak class="mt-3">
                    <select name="sender_unit_id"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- pilih unit pengirim --</option>
                        @foreach ($senderUnits as $unit)
                            <option value="{{ $unit->id }}" @selected(old('sender_unit_id', $letter?->sender_unit_id) == $unit->id)>
                                {{ $unit->name }} @if ($unit->abbr) ({{ $unit->abbr }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="senderMode === 'manual'" x-cloak class="mt-3">
                    <input type="text" name="sender_name" maxlength="255"
                           value="{{ old('sender_name', $letter?->sender_name) }}"
                           placeholder="Nama pengirim"
                           class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-1 text-sm font-semibold text-slate-900">Tujuan</h2>
                <p class="mb-4 text-xs text-slate-500">Instruksi disposisi diisi terpisah oleh Sekretaris Direksi/Kepala Bagian setelah surat diterima.</p>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Kepada (Direktur Utama Tujuan)</label>
                    <select name="director_id"
                            class="mt-1 block w-full max-w-md rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- pilih --</option>
                        @foreach ($directors as $director)
                            <option value="{{ $director->id }}" @selected(old('director_id', $letter?->director_id) == $director->id)>
                                {{ $director->name }} ({{ $director->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @php
                    $selectedDirectors = old('director_recipients', $letter?->directorRecipients->pluck('id')->all() ?? []);
                    $selectedDepartments = old('department_recipients', $letter?->departmentRecipients->pluck('id')->all() ?? []);
                @endphp

                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-700">Tembusan Direksi Lain</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($directors as $director)
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="director_recipients[]" value="{{ $director->id }}"
                                       @checked(in_array($director->id, $selectedDirectors))
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                {{ $director->code }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-sm font-medium text-slate-700">Tembusan Bagian</p>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach ($departments as $department)
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="department_recipients[]" value="{{ $department->id }}"
                                       @checked(in_array($department->id, $selectedDepartments))
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                {{ $department->code }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ $letter ? route('letters.show', $letter) : route('letters.index') }}"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</a>
                <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
