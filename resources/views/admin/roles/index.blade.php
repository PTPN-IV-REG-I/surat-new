@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('breadcrumb', 'Administrasi Sistem / Roles')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <h1 class="text-lg font-semibold text-slate-900">Roles &amp; Permissions</h1>
        <p class="text-sm text-slate-500">
            Daftar role sudah ditentukan lewat <code class="rounded bg-slate-100 px-1">RolePermissionSeeder</code>
            (arsitektur.md §10). Yang bisa diubah di sini hanya kombinasi permission per role.
        </p>

        @foreach ($roles as $role)
            <form method="POST" action="{{ route('admin.roles.update', $role) }}"
                  class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-emerald-700">{{ $role->name }}</h2>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($permissions as $permission)
                        <label class="flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                   @checked($role->permissions->contains('name', $permission->name))
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            {{ $permission->name }}
                        </label>
                    @endforeach
                </div>

                <button class="mt-4 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Simpan {{ $role->name }}
                </button>
            </form>
        @endforeach
    </div>
@endsection
