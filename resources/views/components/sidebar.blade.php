<div
    x-show="sidebarOpen"
    x-transition:enter="transition ease-in-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
    @click="sidebarOpen = false"
    style="display: none;"
></div>

<aside
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-slate-900 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex h-16 items-center gap-2 border-b border-slate-800 px-6">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white">S</div>
        <span class="text-lg font-semibold text-white">Surat</span>
    </div>

    <nav class="space-y-1 px-3 py-4">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            Dashboard
        </a>

        <a href="{{ route('letters.index') }}"
           class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('letters.index') || request()->routeIs('letters.show') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            Arsip Surat
        </a>

        @can('letters.create')
            <a href="{{ route('letters.create') }}"
               class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('letters.create') || request()->routeIs('letters.edit') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Input Surat
            </a>
        @endcan

        <a href="{{ route('agenda-book.index') }}"
           class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('agenda-book.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
            Buku Agenda
        </a>

        @canany(['admin.users', 'admin.roles'])
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-500">Administrasi Sistem</p>

                @can('admin.users')
                    <a href="{{ route('admin.users.index') }}"
                       class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Users
                    </a>
                @endcan

                @can('admin.roles')
                    <a href="{{ route('admin.roles.index') }}"
                       class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-emerald-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Roles &amp; Permissions
                    </a>
                @endcan
            </div>
        @endcanany
    </nav>
</aside>
