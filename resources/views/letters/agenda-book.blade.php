@extends('layouts.app')

@section('title', 'Buku Agenda')
@section('breadcrumb', 'Buku Agenda')

@section('content')
    <div class="mx-auto max-w-5xl space-y-4">
        <h1 class="text-lg font-semibold text-slate-900">Buku Agenda {{ $year }}</h1>

        <form method="GET" class="flex flex-wrap gap-2">
            <select name="year"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                @foreach (range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                @endforeach
            </select>

            <select name="letter_type"
                    class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Semua Jenis</option>
                @foreach (['I', 'II', 'III', 'IV', 'V'] as $type)
                    <option value="{{ $type }}" @selected($letterType === $type)>Jenis {{ $type }}</option>
                @endforeach
            </select>

            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Terapkan</button>
        </form>

        @forelse ($letters as $type => $group)
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                    Jenis {{ $type }}
                </div>

                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-2">No. Agenda</th>
                            <th class="px-4 py-2">No. Surat</th>
                            <th class="px-4 py-2">Tgl Terima</th>
                            <th class="px-4 py-2">Kepada</th>
                            <th class="px-4 py-2">Dari</th>
                            <th class="px-4 py-2">Hal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($group as $letter)
                            <tr>
                                <td class="px-4 py-2 font-medium text-slate-900">{{ $letter->agenda_no }}{{ $letter->agenda_series }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('letters.show', $letter) }}" class="text-emerald-600 hover:underline">{{ $letter->letter_no }}</a>
                                </td>
                                <td class="px-4 py-2 text-slate-600">{{ $letter->received_date?->format('d-m-Y') ?? '-' }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $letter->director?->name ?? '-' }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $letter->senderUnit?->name ?? $letter->sender_name ?? '-' }}</td>
                                <td class="px-4 py-2 max-w-xs truncate text-slate-600" title="{{ $letter->subject }}">{{ $letter->subject }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center text-sm text-slate-400 shadow-sm">
                Tidak ada surat untuk tahun {{ $year }}.
            </div>
        @endforelse
    </div>
@endsection
