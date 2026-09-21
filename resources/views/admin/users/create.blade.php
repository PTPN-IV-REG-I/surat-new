@extends('layouts.app')

@section('title', 'Tambah User')
@section('breadcrumb', 'Administrasi Sistem / Users / Tambah')

@section('content')
    <div class="mx-auto max-w-lg">
        <h1 class="mb-4 text-lg font-semibold text-slate-900">Tambah User</h1>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4" x-data="{ role: '{{ old('role') }}' }">
                @csrf
                @include('admin.users._form', ['user' => null])

                <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
