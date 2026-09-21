@extends('layouts.app')

@section('title', 'Evaluasi Tindak Lanjut')
@section('breadcrumb', 'Laporan / Evaluasi Tindak Lanjut')

@section('content')
    <div class="mx-auto max-w-5xl space-y-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Evaluasi Tindak Lanjut</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $letters->total() }} surat belum ditindaklanjuti, termasuk
                <span class="font-semibold text-red-600">{{ $overdueCount }}</span> yang sudah &ge; 7 hari.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nomor</th>
                        <th class="px-4 py-3">Tgl Terima</th>
                        <th class="px-4 py-3">Menunggu</th>
                        <th class="px-4 py-3">Kepada</th>
                        <th class="px-4 py-3">Dibuat Oleh</th>
                        <th class="px-4 py-3">Hal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($letters as $letter)
                        @php
                            $daysWaiting = $letter->received_date ? $letter->received_date->diffInDays(now()) : null;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $letter->letter_no }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->received_date?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($daysWaiting === null)
                                    -
                                @elseif ($daysWaiting >= 7)
                                    <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">{{ $daysWaiting }} hari</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $daysWaiting }} hari</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->director?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $letter->creator?->name ?? '-' }}</td>
                            <td class="px-4 py-3 max-w-xs truncate text-slate-600" title="{{ $letter->subject }}">{{ $letter->subject }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('letters.show', $letter) }}" class="text-emerald-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Semua surat sudah ditindaklanjuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $letters->links() }}
    </div>
@endsection
