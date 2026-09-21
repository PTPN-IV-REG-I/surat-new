@extends('layouts.app')

@section('title', $division ? 'Edit Surat Bagian' : 'Input Surat Bagian')
@section('breadcrumb', 'Surat Bagian / ' . ($division ? 'Edit' : 'Input'))

@section('content')
    <div class="mx-auto max-w-3xl space-y-4">
        <h1 class="text-lg font-semibold text-slate-900">{{ $division ? "Edit Surat Bagian: {$division->letter_no}" : 'Input Surat Bagian' }}</h1>

        <form method="POST"
              action="{{ $division ? route('letter-divisions.update', $division) : route('letter-divisions.store') }}"
              class="space-y-6">
            @csrf
            @if ($division)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-semibold text-slate-900">Info Surat</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jenis</label>
                        <input type="text" name="division_type" maxlength="10"
                               value="{{ old('division_type', $division?->division_type) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Kode Tipe Agenda</label>
                        @if ($division)
                            <input type="text" value="{{ $division->agenda_no }}/{{ $division->agenda_type_code }}" disabled
                                   class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">No. Agenda tidak bisa diubah setelah dibuat.</p>
                        @else
                            <input type="text" name="agenda_type_code" required maxlength="20"
                                   value="{{ old('agenda_type_code') }}"
                                   placeholder="mis. SP-III"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm uppercase focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <p class="mt-1 text-xs text-slate-400">No. Agenda dibuat otomatis saat disimpan.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tgl Agenda</label>
                        <input type="date" name="agenda_date"
                               value="{{ old('agenda_date', $division?->agenda_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        @unless ($division)
                            <p class="mt-1 text-xs text-slate-400">Menentukan tahun No. Agenda. Kosongkan untuk pakai Tgl Terima.</p>
                        @endunless
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">No. Surat</label>
                        <input type="text" name="letter_no" maxlength="100"
                               value="{{ old('letter_no', $division?->letter_no) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tgl Surat</label>
                        <input type="date" name="letter_date"
                               value="{{ old('letter_date', $division?->letter_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tgl Terima</label>
                        <input type="date" name="received_date"
                               value="{{ old('received_date', $division?->received_date?->format('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Jumlah Lembar</label>
                        <input type="number" step="0.01" name="amount" min="0"
                               value="{{ old('amount', $division?->amount) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Status</label>
                        <select name="status"
                                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <option value="">-- pilih --</option>
                            @foreach (['Sangat Segera', 'Segera', 'Rahasia', 'Biasa'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $division?->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Kepada</label>
                        <textarea name="addressee" rows="2"
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('addressee', $division?->addressee) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Dari</label>
                        <input type="text" name="sender_name" maxlength="255"
                               value="{{ old('sender_name', $division?->sender_name) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Urusan</label>
                        <input type="text" name="matter" maxlength="255"
                               value="{{ old('matter', $division?->matter) }}"
                               class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Hal</label>
                        <textarea name="subject" required rows="3"
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('subject', $division?->subject) }}</textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Catatan</label>
                        <textarea name="note" rows="2"
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('note', $division?->note) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-3 text-sm font-semibold text-slate-900">Tembusan Direksi</h2>

                @php
                    $selectedDirectors = old('director_recipients', $division?->directorRecipients->pluck('id')->all() ?? []);
                @endphp

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
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

            <div class="flex justify-end gap-2">
                <a href="{{ $division ? route('letter-divisions.show', $division) : route('letter-divisions.index') }}"
                   class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Batal</a>
                <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
