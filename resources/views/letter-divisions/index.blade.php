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

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <table id="divisions-table" class="w-full text-sm">
                <thead>
                    <tr>
                        <th>No. Agenda</th>
                        <th>No. Surat</th>
                        <th>Tgl Terima</th>
                        <th>Dari</th>
                        <th>Hal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            window.$('#divisions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: @json(route('letter-divisions.data', [], false)),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.$('meta[name="csrf-token"]').attr('content'),
                    },
                },
                columns: [
                    { data: 'agenda', name: 'agenda', className: 'font-medium text-slate-900' },
                    { data: 'letter_no', name: 'letter_no' },
                    { data: 'received_date', name: 'received_date' },
                    { data: 'sender_name', name: 'sender_name' },
                    { data: 'subject', name: 'subject', className: 'max-w-xs truncate' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' },
                ],
                order: [[2, 'desc']],
                pageLength: 20,
            });
        });
    </script>
@endsection
