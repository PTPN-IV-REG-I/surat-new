@extends('layouts.app')

@section('title', $user ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('breadcrumb', 'Administrasi Sistem / Pengguna / ' . ($user ? 'Edit' : 'Tambah'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6 pb-12">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-2xs transition hover:bg-slate-50 hover:text-slate-800 hover:border-slate-300"
                   title="Kembali ke daftar pengguna">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg font-bold text-slate-900">
                            {{ $user ? "Edit Pengguna: {$user->name}" : 'Tambah Pengguna Baru' }}
                        </h1>
                        @if ($user)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-[#033F63]/10 text-[#033F63]">
                                {{ $user->username }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $user ? 'Perbarui informasi profil dan hak akses pengguna' : 'Lengkapi formulir untuk membuat akun pengguna baru' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <x-button variant="secondary" href="{{ route('admin.users.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" form="user-form" variant="primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Simpan</span>
                </x-button>
            </div>
        </div>

        {{-- Error Summary Alert --}}
        @if (isset($errors) && $errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50/80 p-4 text-xs text-rose-800 shadow-xs">
                <div class="flex items-center gap-2 font-bold mb-1.5 text-rose-900">
                    <svg class="h-4 w-4 shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="user-form"
              method="POST"
              action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}"
              class="space-y-6"
              x-data="{ role: '{{ old('role', $user?->getRoleNames()->first()) }}' }">
            @csrf
            @if ($user)
                @method('PUT')
            @endif

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Informasi Pengguna</h2>
                        <p class="text-[11px] text-slate-400">Data login dan identitas pegawai</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    {{-- Username --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username', $user?->username) }}" required
                               placeholder="Nama akun untuk login..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            NIK Pegawai <span class="text-slate-400 text-[10px] font-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="nik" value="{{ old('nik', $user?->nik) }}" maxlength="20"
                               placeholder="Nomor Induk Karyawan..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                               placeholder="Nama lengkap pejabat / pegawai..."
                               class="w-full h-10 rounded-xl border border-slate-200 bg-slate-50/60 px-3.5 text-xs font-medium text-slate-700 placeholder-slate-400 shadow-2xs transition hover:bg-white hover:border-slate-300 focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-xs space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#033F63]/10 text-[#033F63]">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Peran &amp; Hak Akses</h2>
                        <p class="text-[11px] text-slate-400">Tentukan otoritas dan bagian penugasan akun</p>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- Role --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Role / Otoritas Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <x-select name="role" x-model="role" required wrapperClass="w-full">
                            <option value="">-- Pilih Role Pengguna --</option>
                            @foreach ($roles as $roleName)
                                <option value="{{ $roleName }}">{{ $roleName }}</option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Direktur (untuk role director-secretary) --}}
                    <div x-show="role === 'director-secretary'" x-cloak class="pt-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Direktur yang Dilayani <span class="text-rose-500">*</span>
                        </label>
                        <x-select name="director_id" wrapperClass="w-full">
                            <option value="">-- Pilih Direktur / SEVP --</option>
                            @foreach ($directors as $director)
                                <option value="{{ $director->id }}" @selected(old('director_id', $user?->director_id) == $director->id)>
                                    {{ $director->name }} ({{ $director->code }})
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Bagian (untuk role department-head) --}}
                    <div x-show="role === 'department-head'" x-cloak class="pt-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Bagian / Unit Kerja yang Dipimpin <span class="text-rose-500">*</span>
                        </label>
                        <x-select name="department_id" wrapperClass="w-full">
                            <option value="">-- Pilih Bagian / Biro --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('department_id', $user?->department_id) == $department->id)>
                                    {{ $department->code }} - {{ $department->name }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    {{-- Status Akun Aktif / Password Notice --}}
                    <div class="pt-3 border-t border-slate-100">
                        @if ($user)
                            <label class="group relative flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer transition hover:bg-white hover:border-slate-300 select-none has-[:checked]:border-[#033F63] has-[:checked]:bg-[#033F63]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#033F63]">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))
                                       class="h-4 w-4 rounded border-slate-300 text-[#033F63] focus:ring-[#033F63]">
                                <div>
                                    <div class="text-xs font-bold text-slate-800 group-has-[:checked]:text-[#033F63]">Akun Aktif</div>
                                    <div class="text-[11px] text-slate-400">Bila dinonaktifkan, pengguna tidak dapat masuk ke aplikasi.</div>
                                </div>
                            </label>
                        @else
                            <div class="flex items-start gap-2.5 p-3.5 rounded-xl border border-sky-200 bg-sky-50/70 text-xs text-sky-800">
                                <svg class="h-4 w-4 shrink-0 text-sky-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                                </svg>
                                <div>
                                    <strong class="font-bold">Informasi Kata Sandi Awal:</strong>
                                    <p class="mt-0.5 text-sky-700/90 text-[11px]">
                                        Password awal diset otomatis ke default: <strong class="font-bold text-sky-900">12345678</strong>.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <x-button variant="secondary" href="{{ route('admin.users.index') }}">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>{{ $user ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</span>
                </x-button>
            </div>
        </form>
    </div>
@endsection
