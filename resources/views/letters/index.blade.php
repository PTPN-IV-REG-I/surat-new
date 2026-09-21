@extends('layouts.app')

@section('title', 'Arsip Surat')
@section('breadcrumb', 'Arsip Surat')

@section('content')
    <div class="mx-auto max-w-6xl space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-slate-900">Arsip Surat</h1>

            @can('letters.create')
                <a href="{{ route('letters.create') }}"
                   class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    + Input Surat
                </a>
            @endcan
        </div>

        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nomor / hal / dari / kata kunci..."
                   class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">

            <select name="month"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Semua Bulan</option>
                @foreach (['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $label)
                    <option value="{{ $i + 1 }}" @selected($month == $i + 1)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="year"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Semua Tahun</option>
                @foreach (range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endforeach
            </select>

            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cari</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nomor</th>
                        <th class="px-4 py-3">Tgl Surat</th>
                        <th class="px-4 py-3">Tgl Terima</th>
                        <th class="px-4 py-3">No Agenda</th>
                        <th class="px-4 py-3">Kepada</th>
                        <th class="px-4 py-3">Dari</th>
                        <th class="px-4 py-3">Hal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($letters as $letter)
                        @php
                            $telat = ! $letter->follow_up
                                && $letter->received_date
                                && $letter->received_date->diffInDays(now()) >= 7;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $letter->letter_no }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->letter_date?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->received_date?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->letter_type }}-{{ $letter->agenda_no }}{{ $letter->agenda_series }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->director?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}</td>
                            <td class="px-4 py-3 max-w-xs truncate text-slate-600" title="{{ $letter->subject }}">{{ $letter->subject }}</td>
                            <td class="px-4 py-3">
                                @if ($letter->follow_up)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Sudah Ditindaklanjuti</span>
                                @elseif ($telat)
                                    <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">Belum Ditindaklanjuti</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Belum Ditindaklanjuti</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('letters.show', $letter) }}" class="text-emerald-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-slate-400">Tidak ada surat ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $letters->links() }}
    </div>
@endsection
