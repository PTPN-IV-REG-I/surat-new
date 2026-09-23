@extends('layouts.app')

@section('title', 'Evaluasi Tindak Lanjut')
@section('breadcrumb', 'Laporan / Evaluasi Tindak Lanjut')

@section('content')
    <div class="mx-auto max-w-5xl space-y-4">
        <div>
            <h1 class="text-lg font-semibold text-slate-900">Evaluasi Tindak Lanjut</h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $total }} surat belum ditindaklanjuti, termasuk
                <span class="font-semibold text-red-600">{{ $overdueCount }}</span> yang sudah &ge; 7 hari.
            </p>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <table id="follow-up-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Tgl Terima</th>
                        <th>Menunggu</th>
                        <th>Kepada</th>
                        <th>Dibuat Oleh</th>
                        <th>Hal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            window.$('#follow-up-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: @json(route('reports.follow-up.data', [], false)),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.$('meta[name="csrf-token"]').attr('content'),
                    },
                },
                columns: [
                    { data: 'letter_no', name: 'letter_no', className: 'font-medium text-slate-900' },
                    { data: 'received_date', name: 'received_date' },
                    { data: 'waiting', name: 'waiting', orderable: false, searchable: false },
                    { data: 'director_name', name: 'director_name', orderable: false, searchable: false },
                    { data: 'creator_name', name: 'creator_name', orderable: false, searchable: false },
                    { data: 'subject', name: 'subject', className: 'max-w-xs truncate' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' },
                ],
                order: [[1, 'asc']],
                pageLength: 20,
                language: {
                    emptyTable: 'Semua surat sudah ditindaklanjuti.',
                },
            });
        });
    </script>
@endsection
