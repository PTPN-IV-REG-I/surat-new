@extends('layouts.app')

@section('title', 'Surat Bagian')
@section('breadcrumb', 'Surat Bagian')

@section('content')
    <div class="mx-auto max-w-5xl space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-slate-900">Surat Bagian</h1>

            <a href="{{ route('letter-divisions.create') }}"
               class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                + Input Surat Bagian
            </a>
        </div>

        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari no. surat / hal / dari..."
                   class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cari</button>
        </form>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">No. Agenda</th>
                        <th class="px-4 py-3">No. Surat</th>
                        <th class="px-4 py-3">Tgl Terima</th>
                        <th class="px-4 py-3">Dari</th>
                        <th class="px-4 py-3">Hal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($divisions as $division)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $division->agenda_no }}/{{ $division->agenda_type_code }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $division->letter_no ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $division->received_date?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $division->sender_name ?? '-' }}</td>
                            <td class="px-4 py-3 max-w-xs truncate text-slate-600" title="{{ $division->subject }}">{{ $division->subject }}</td>
                            <td class="px-4 py-3">
                                @if ($division->status)
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $division->status }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('letter-divisions.show', $division) }}" class="text-emerald-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-400">Tidak ada surat bagian ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $divisions->links() }}
    </div>
@endsection
