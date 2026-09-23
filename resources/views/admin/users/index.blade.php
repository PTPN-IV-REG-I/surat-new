@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Administrasi Sistem / Manajemen Pengguna')

@section('content')
<div class="space-y-6 pb-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola data akun pengguna, peran hak akses (role), dan status otorisasi pegawai di lingkungan PT Perkebunan Nusantara</p>
        </div>

        <div class="flex items-center gap-2.5">
            <x-button variant="primary" href="{{ route('admin.users.create') }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Tambah Pengguna</span>
            </x-button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">TOTAL PENGGUNA</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($totalUsers, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">akun</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-[#0B527E] ring-1 ring-cyan-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                <span>Terdaftar di sistem persuratan</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">PENGGUNA AKTIF</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($activeUsers, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">akun</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200/60">
                Otorisasi Aktif
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">AKUN NONAKTIF</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($inactiveUsers, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">akun</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 ring-1 ring-rose-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold text-rose-700 ring-1 ring-rose-200/60">
                Akses Dibatasi
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs flex flex-col justify-between space-y-3">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">ADMINISTRATOR</span>
                    <div class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ number_format($adminUsers, 0, ',', '.') }} <span class="text-xs font-medium text-slate-500">akun</span>
                    </div>
                </div>
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#033F63] ring-1 ring-blue-200/50">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            </div>
            <div class="w-fit rounded-full bg-[#033F63]/10 px-2.5 py-0.5 text-[10px] font-bold text-[#033F63] ring-1 ring-[#033F63]/20">
                Hak Akses Penuh
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs"
         x-data="{ search: '{{ addslashes(request('search', '')) }}' }">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3 w-full">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

            <div class="relative flex-1 min-w-[240px]">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <input type="text" name="search" x-model="search"
                       placeholder="Cari nama, username, atau NIK pegawai..."
                       class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 pl-10 pr-9 text-xs text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">

                <button type="button"
                        x-show="search.length > 0"
                        x-cloak
                        @click="search = ''; $nextTick(() => $el.closest('form').submit())"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 transition cursor-pointer"
                        title="Hapus pencarian">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <x-select name="role" wrapperClass="w-full sm:w-48">
                    <option value="">Semua Peran / Role</option>
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption }}" @selected(request('role') == $roleOption)>
                            {{ $roleOption }}
                        </option>
                    @endforeach
                </x-select>

                <x-select name="status" wrapperClass="w-full sm:w-40">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </x-select>

                <x-button type="submit" variant="primary" class="w-full sm:w-auto">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <span>Filter</span>
                </x-button>

                @if (request()->hasAny(['search', 'role', 'status']))
                    <x-button variant="danger" href="{{ route('admin.users.index') }}" title="Reset semua filter" class="w-full sm:w-auto">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        <span>Reset</span>
                    </x-button>
                @endif
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[1080px]">
                <thead>
                    <tr class="border-b border-slate-200/80 bg-[#EAF5EF] text-[10.5px] font-extrabold uppercase tracking-wider text-emerald-950">
                        <th class="px-3 py-3.5 w-12 text-center">NO</th>
                        <th class="px-4 py-3.5 w-40">USERNAME</th>
                        <th class="px-4 py-3.5 min-w-[240px]">NAMA &amp; NIK</th>
                        <th class="px-4 py-3.5 w-48">ROLE / HAK AKSES</th>
                        <th class="px-4 py-3.5 min-w-[220px]">UNIT KERJA / SCOPE</th>
                        <th class="px-4 py-3.5 w-32">STATUS</th>
                        <th class="px-3 py-3.5 w-16 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($users as $user)
                        @php
                            $roleName = $user->getRoleNames()->first() ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-3 py-4 text-center align-top font-bold text-slate-400 text-xs">
                                {{ $users->firstItem() + $loop->index }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-extrabold text-emerald-800 ring-1 ring-emerald-200">
                                    {{ $user->username }}
                                </span>
                                <div class="mt-1 text-[10px] text-slate-400 font-semibold tracking-wider uppercase">
                                    Akun Sistem
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <div class="font-bold text-slate-900 text-xs leading-snug">
                                    {{ $user->name }}
                                </div>
                                <div class="mt-1.5">
                                    @if ($user->nik)
                                        <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">
                                            NIK: {{ $user->nik }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">
                                            Belum ada NIK
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/70">
                                    {{ $roleName }}
                                </span>
                            </td>

                            <td class="px-4 py-4 align-top text-xs">
                                @if ($user->director)
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Direksi:</span>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center rounded bg-sky-50 px-2 py-0.5 text-[11px] font-bold text-sky-700 ring-1 ring-sky-200/60">
                                                {{ $user->director->code }}
                                            </span>
                                            <div class="text-[11px] font-semibold text-slate-700 mt-0.5">{{ $user->director->name }}</div>
                                        </div>
                                    </div>
                                @elseif ($user->department)
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bagian:</span>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center rounded bg-cyan-50 px-2 py-0.5 text-[11px] font-bold text-[#0B527E] ring-1 ring-cyan-200/60">
                                                Bagian {{ $user->department->code }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Cakupan:</span>
                                        <div class="text-[11px] text-slate-400 italic mt-0.5">Semua Unit / Global</div>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-4 align-top">
                                @if ($user->is_active)
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-400">
                                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-3 py-4 text-center align-top">
                                <div class="flex flex-col items-center gap-1.5">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       title="Ubah / Edit Pengguna"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 hover:border-amber-300 shadow-2xs transition cursor-pointer">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </a>

                                    {{-- Tombol Reset Password via Confirm Alert Modal --}}
                                    <button
                                        type="button"
                                        @click="$dispatch('open-confirm-modal', {
                                            title: 'Reset Kata Sandi Akun',
                                            message: 'Apakah Anda yakin ingin mengatur ulang kata sandi untuk pengguna {{ addslashes($user->name) }} ({{ $user->username }}) ke password default (12345678)?',
                                            confirmText: 'Reset Sandi',
                                            variant: 'sky',
                                            action: '{{ route('admin.users.reset-password', $user) }}',
                                            method: 'POST'
                                        })"
                                        title="Reset Password Akun"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-600 hover:bg-sky-100 hover:border-sky-300 shadow-2xs transition cursor-pointer"
                                    >
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                        </svg>
                                    </button>

                                    {{-- Tombol Nonaktifkan Pengguna via Confirm Alert Modal --}}
                                    @if ($user->is_active && $user->username !== 'legacy-migration')
                                        <button
                                            type="button"
                                            @click="$dispatch('open-confirm-modal', {
                                                title: 'Nonaktifkan Akun Pengguna',
                                                message: 'Apakah Anda yakin ingin menonaktifkan akun {{ addslashes($user->name) }} ({{ $user->username }})? Pengguna tidak dapat lagi masuk ke sistem.',
                                                confirmText: 'Nonaktifkan',
                                                variant: 'danger',
                                                action: '{{ route('admin.users.destroy', $user) }}',
                                                method: 'DELETE'
                                            })"
                                            title="Nonaktifkan Pengguna"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 hover:border-rose-300 shadow-2xs transition cursor-pointer"
                                        >
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="8" x2="23" y2="14"/><line x1="23" y1="8" x2="17" y2="14"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-sm font-bold text-slate-700">Tidak ada pengguna yang cocok</div>
                                <p class="text-xs text-slate-400 mt-0.5">Silakan sesuaikan kata kunci pencarian atau filter peran yang dipilih.</p>
                                @if (request()->hasAny(['search', 'role', 'status']))
                                    <div class="mt-3">
                                        <x-button variant="secondary" size="sm" href="{{ route('admin.users.index') }}">
                                            Reset Filter
                                        </x-button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 px-5 py-4 bg-white text-xs text-slate-600">
            <div>
                Menampilkan <span class="font-bold text-slate-800">{{ $users->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ number_format($users->total(), 0, ',', '.') }}</span> pengguna
            </div>

            <div class="flex items-center gap-2">
                <span class="text-slate-500 text-xs">Baris per halaman:</span>
                <x-select name="per_page" onchange="window.location.href = this.value;" wrapperClass="w-20">
                    @foreach ([10, 25, 50, 100] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" {{ $users->perPage() == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </x-select>
            </div>

            <div>
                {{ $users->links('letters._pagination') }}
            </div>
        </div>
    </div>

</div>
@endsection
