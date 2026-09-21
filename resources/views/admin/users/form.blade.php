@extends('layouts.app')

@section('title', $user ? 'Edit User' : 'Tambah User')
@section('breadcrumb', 'Administrasi Sistem / Users / ' . ($user ? 'Edit' : 'Tambah'))

@section('content')
    <div class="mx-auto max-w-lg">
        <h1 class="mb-4 text-lg font-semibold text-slate-900">{{ $user ? "Edit User: {$user->username}" : 'Tambah User' }}</h1>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}"
                  class="space-y-4"
                  x-data="{ role: '{{ old('role', $user?->getRoleNames()->first()) }}' }">
                @csrf
                @if ($user)
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user?->username) }}" required
                           class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                           class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">NIK (opsional, referensi ke data pegawai)</label>
                    <input type="text" name="nik" value="{{ old('nik', $user?->nik) }}" maxlength="20"
                           class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" x-model="role" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- pilih role --</option>
                        @foreach ($roles as $roleName)
                            <option value="{{ $roleName }}">{{ $roleName }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="role === 'director-secretary'" x-cloak>
                    <label class="block text-sm font-medium text-slate-700">Direktur yang Dilayani</label>
                    <select name="director_id"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- pilih direktur --</option>
                        @foreach ($directors as $director)
                            <option value="{{ $director->id }}" @selected(old('director_id', $user?->director_id) == $director->id)>
                                {{ $director->name }} ({{ $director->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="role === 'department-head'" x-cloak>
                    <label class="block text-sm font-medium text-slate-700">Bagian yang Dipimpin</label>
                    <select name="department_id"
                            class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="">-- pilih bagian --</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $user?->department_id) == $department->id)>
                                {{ $department->code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($user)
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        Akun aktif
                    </label>
                @else
                    <p class="text-xs text-slate-500">
                        Password sementara akan digenerate otomatis dan ditampilkan sekali setelah user dibuat —
                        wajib diganti saat login pertama.
                    </p>
                @endif

                <button class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan
                </button>
            </form>
        </div>
    </div>
@endsection
