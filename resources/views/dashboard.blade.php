@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-lg font-semibold text-slate-900">Selamat datang, {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                Peran: <span class="font-medium text-slate-700">{{ $user->getRoleNames()->implode(', ') ?: '-' }}</span>
                @if ($user->director)
                    &middot; Direktur: <span class="font-medium text-slate-700">{{ $user->director->name }}</span>
                @endif
                @if ($user->department)
                    &middot; Bagian: <span class="font-medium text-slate-700">{{ $user->department->code }}</span>
                @endif
            </p>

            <div class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-600">
                Fitur Surat (input, arsip, disposisi) sedang dalam pengembangan. Halaman ini adalah fondasi
                autentikasi &amp; administrasi sistem.
            </div>
        </div>
    </div>
@endsection
