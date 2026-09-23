@extends('layouts.app')

@section('title', 'Role & Hak Akses')
@section('breadcrumb', 'Administrasi Sistem / Role & Izin')

@section('content')
<div class="space-y-6 pb-8">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Role &amp; Hak Akses</h1>
            <p class="text-xs text-slate-500 mt-1">Konfigurasi matriks kewenangan dan hak akses per peran pengguna pada sistem persuratan PTPN</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-2xs">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span>{{ $roles->count() }} Role Aktif</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 shadow-2xs">
                <svg class="h-3.5 w-3.5 text-[#033F63]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                <span>{{ $permissions->count() }} Permission</span>
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-[#0B527E] ring-1 ring-cyan-200/50 mt-0.5">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xs font-bold text-slate-800">Petunjuk Otorisasi Berbasis Peran (RBAC)</h2>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Daftar peran telah dibakukan melalui struktur sistem. Anda dapat menyesuaikan kombinasi hak akses (permission) untuk tiap peran di bawah ini. Centang izin yang diperbolehkan lalu klik tombol simpan pada masing-masing peran.
                </p>
            </div>
        </div>
    </div>

    @php
        // Helper metadata untuk peran
        $roleMeta = [
            'admin' => [
                'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80 ring-emerald-500/10',
                'icon_bg' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/60',
                'title' => 'Administrator',
                'desc' => 'Pengelola sistem utama dengan akses konfigurasi menyeluruh, pengguna, dan semua arsip surat.',
            ],
            'department-head' => [
                'badge' => 'bg-cyan-50 text-[#0B527E] border-cyan-200/80 ring-cyan-500/10',
                'icon_bg' => 'bg-cyan-50 text-[#0B527E] ring-cyan-200/60',
                'title' => 'Kepala Bagian (Department Head)',
                'desc' => 'Pimpinan unit kerja/bagian yang mengelola pembagian surat bagian dan arsip tingkat bagian.',
            ],
            'director-secretary' => [
                'badge' => 'bg-sky-50 text-sky-800 border-sky-200/80 ring-sky-500/10',
                'icon_bg' => 'bg-sky-50 text-sky-700 ring-sky-200/60',
                'title' => 'Sekretaris Direksi (Director Secretary)',
                'desc' => 'Staf sekretariat direksi yang berwenang memantau surat direksi dan menginput lembar disposisi.',
            ],
            'garden-officer' => [
                'badge' => 'bg-amber-50 text-amber-800 border-amber-200/80 ring-amber-500/10',
                'icon_bg' => 'bg-amber-50 text-amber-700 ring-amber-200/60',
                'title' => 'Petugas Kebun / Unit (Garden Officer)',
                'desc' => 'Operator unit operasional/kebun untuk pendaftaran surat masuk, disposisi, dan berkas unit.',
            ],
        ];

        // Kelompok izin agar mudah dibaca & rapi
        $permissionGroups = [
            'Arsip Surat Masuk' => [
                'letters.view-all' => ['label' => 'Lihat Semua Surat', 'desc' => 'Dapat melihat seluruh arsip surat lintas unit/direksi'],
                'letters.view-director' => ['label' => 'Lihat Surat Direksi', 'desc' => 'Melihat arsip surat masuk lingkup direksi'],
                'letters.view-department' => ['label' => 'Lihat Surat Bagian', 'desc' => 'Melihat arsip surat masuk lingkup bagiannya'],
                'letters.view-own-garden' => ['label' => 'Lihat Surat Kebun Sendiri', 'desc' => 'Hanya melihat surat yang dibuat oleh kebun sendiri'],
                'letters.create' => ['label' => 'Tambah / Registrasi Surat', 'desc' => 'Dapat mendaftarkan surat masuk baru ke sistem'],
                'letters.update' => ['label' => 'Edit / Ubah Surat', 'desc' => 'Mengubah data nomor, perihal, dan berkas surat'],
                'letters.delete' => ['label' => 'Hapus Arsip Surat', 'desc' => 'Menghapus data surat masuk dari basis data'],
            ],
            'Disposisi & Bagian' => [
                'letters.dispose' => ['label' => 'Input / Kelola Disposisi', 'desc' => 'Membuat dan memperbarui instruksi lembar disposisi'],
                'letter-divisions.manage' => ['label' => 'Kelola Surat Bagian', 'desc' => 'Mengatur tindak lanjut dan distribusi surat bagian'],
            ],
            'Konfigurasi & Administrasi' => [
                'references.manage' => ['label' => 'Kelola Data Referensi', 'desc' => 'Pengaturan master direksi, bagian, dan agenda'],
                'admin.users' => ['label' => 'Manajemen Pengguna', 'desc' => 'Mengelola akun pegawai, tambah, edit, reset sandi'],
                'admin.roles' => ['label' => 'Konfigurasi Role & Izin', 'desc' => 'Mengubah matriks hak akses ini'],
            ],
        ];
    @endphp

    <div class="space-y-5">
        @foreach ($roles as $role)
            @php
                $meta = $roleMeta[$role->name] ?? [
                    'badge' => 'bg-slate-50 text-slate-700 border-slate-200/80 ring-slate-500/10',
                    'icon_bg' => 'bg-slate-100 text-slate-700 ring-slate-200/60',
                    'title' => strtoupper($role->name),
                    'desc' => 'Peran pengguna terdaftar dalam sistem persuratan.',
                ];
                $rolePermissionNames = $role->permissions->pluck('name')->toArray();
                $totalActive = count($rolePermissionNames);
                $allNames = $permissions->pluck('name')->toArray();
            @endphp

            <div x-data="{
                activePermissions: {{ json_encode($rolePermissionNames) }},
                allPermissions: {{ json_encode($allNames) }},
                selectAll() {
                    this.activePermissions = [...this.allPermissions];
                },
                deselectAll() {
                    this.activePermissions = [];
                },
                isAllSelected() {
                    return this.activePermissions.length === this.allPermissions.length;
                }
            }" class="rounded-2xl border border-slate-200/80 bg-white shadow-xs overflow-hidden transition hover:border-slate-300">

                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf
                    @method('PUT')

                    <div class="p-5 sm:p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 via-white to-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $meta['icon_bg'] }} ring-1 shadow-2xs mt-0.5">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">{{ $meta['title'] }}</h2>
                                    <span class="inline-flex items-center rounded-lg border px-2 py-0.5 text-[10px] font-extrabold tracking-wider uppercase font-mono ring-1 shadow-2xs {{ $meta['badge'] }}">
                                        {{ $role->name }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">{{ $meta['desc'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start md:self-center shrink-0">
                            <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-[#033F63]"></span>
                                <span><strong x-text="activePermissions.length" class="text-slate-900"></strong> / {{ $permissions->count() }} Izin Aktif</span>
                            </div>

                            <button type="button"
                                    @click="isAllSelected() ? deselectAll() : selectAll()"
                                    class="inline-flex h-8 items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 text-[11px] font-bold text-slate-700 shadow-2xs transition hover:bg-slate-50 hover:border-slate-300 cursor-pointer">
                                <span x-text="isAllSelected() ? 'Batal Pilih Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">
                        @foreach ($permissionGroups as $groupTitle => $groupItems)
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-mono">{{ $groupTitle }}</span>
                                    <div class="h-px flex-1 bg-slate-100"></div>
                                </div>

                                <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($groupItems as $permCode => $permDetail)
                                        @php
                                            $existsInDb = $permissions->contains('name', $permCode);
                                        @endphp
                                        @if ($existsInDb)
                                            <label class="group relative flex items-start gap-3 rounded-xl border border-slate-200/80 bg-slate-50/50 p-3 text-xs cursor-pointer select-none transition-all duration-150 hover:bg-white hover:border-slate-300 hover:shadow-2xs has-[:checked]:border-[#033F63] has-[:checked]:bg-[#033F63]/5 has-[:checked]:ring-1 has-[:checked]:ring-[#033F63]">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permCode }}"
                                                       x-model="activePermissions"
                                                       class="h-4 w-4 mt-0.5 shrink-0 rounded border-slate-300 text-[#033F63] focus:ring-[#033F63] cursor-pointer">
                                                <div class="min-w-0 flex-1">
                                                    <div class="font-bold text-slate-800 group-has-[:checked]:text-[#033F63] flex items-center justify-between gap-1">
                                                        <span>{{ $permDetail['label'] }}</span>
                                                    </div>
                                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $permCode }}</div>
                                                    <p class="text-[11px] text-slate-500 mt-1 leading-snug group-has-[:checked]:text-slate-600">
                                                        {{ $permDetail['desc'] }}
                                                    </p>
                                                </div>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-5 py-4 sm:px-6 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-[11px] text-slate-400 italic">
                            * Perubahan hak akses akan langsung berlaku saat pengguna memuat ulang halaman.
                        </div>

                        <x-button type="submit" variant="primary">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            <span>Simpan Hak Akses</span>
                        </x-button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

</div>
@endsection
