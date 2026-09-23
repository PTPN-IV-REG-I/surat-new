<div
    x-show="mobileSidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-xs lg:hidden"
    @click="mobileSidebarOpen = false"
    style="display: none;"
></div>

<aside
    class="fixed inset-y-0 left-0 z-40 flex h-screen shrink-0 flex-col justify-between bg-[#032338] text-slate-300 transition-all duration-300 ease-in-out select-none overflow-hidden lg:sticky lg:top-0"
    :class="{
        'translate-x-0 w-64': mobileSidebarOpen,
        '-translate-x-full lg:translate-x-0': !mobileSidebarOpen,
        'lg:w-64 lg:border-r lg:border-slate-800/80': desktopSidebarOpen,
        'lg:w-0 lg:border-r-0': !desktopSidebarOpen
    }"
>
    <div class="flex h-full w-64 shrink-0 flex-col justify-between">
    <div class="flex h-16 items-center gap-3 border-b border-slate-800/80 px-4">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#28666E] to-[#033F63] text-white shadow-md shadow-black/25 ring-1 ring-white/10">
            <svg class="h-5 w-5 text-[#FEDC97]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
            </svg>
        </div>
        <div class="flex flex-col min-w-0">
            <span class="text-xs font-extrabold tracking-wider text-white">SURAT PTPN</span>
            <span class="text-[9px] font-semibold tracking-widest text-[#7C9885] uppercase">Tata Kelola Persuratan</span>
        </div>
        <button @click="toggleSidebar()"
                class="ml-auto hidden lg:flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white transition cursor-pointer"
                title="Sembunyikan Sidebar"
                aria-label="Sembunyikan Sidebar">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>

        <button @click="mobileSidebarOpen = false" class="ml-auto rounded-lg p-1 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden cursor-pointer" aria-label="Tutup menu">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        <div>
            <div class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                NAVIGASI UTAMA
            </div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#0B527E]/45 text-white ring-1 ring-white/15 shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-[#FEDC97]' : 'text-slate-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="7" x="3" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="14" rx="1.5"/><rect width="7" height="7" x="3" y="14" rx="1.5"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('letters.index') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-medium transition-all {{ request()->routeIs('letters.*') ? 'bg-[#0B527E]/45 text-white font-semibold ring-1 ring-white/15 shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('letters.*') ? 'text-[#FEDC97]' : 'text-slate-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>
                    </svg>
                    <span class="truncate">Arsip Surat Masuk</span>
                    <span class="ml-auto rounded-md bg-[#0B527E] px-2 py-0.5 text-[10px] font-bold text-cyan-200 ring-1 ring-cyan-400/20 shadow-xs">14 Baru</span>
                </a>

                <a href="{{ route('agenda-book.index') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-medium transition-all {{ request()->routeIs('agenda-book.*') ? 'bg-[#0B527E]/45 text-white font-semibold ring-1 ring-white/15 shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('agenda-book.*') ? 'text-[#FEDC97]' : 'text-slate-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10M6 10h10"/>
                    </svg>
                    <span>Buku Agenda</span>
                    <span class="ml-auto text-[10px] font-semibold text-slate-400">I-X</span>
                </a>
            </nav>
        </div>

        @canany(['admin.users', 'admin.roles'])
            <div>
                <div class="flex items-center justify-between px-3 pb-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">ADMINISTRASI</span>
                    <span class="rounded bg-[#0B527E]/60 px-1.5 py-0.5 text-[9px] font-bold text-cyan-300 ring-1 ring-cyan-400/25">Admin</span>
                </div>
                <nav class="space-y-1">
                    @can('admin.users')
                        <a href="{{ route('admin.users.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-medium transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#0B527E]/45 text-white font-semibold ring-1 ring-white/15 shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-[#FEDC97]' : 'text-slate-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span>Manajemen Pengguna</span>
                        </a>
                    @endcan

                    @can('admin.roles')
                        <a href="{{ route('admin.roles.index') }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-medium transition-all {{ request()->routeIs('admin.roles.*') ? 'bg-[#0B527E]/45 text-white font-semibold ring-1 ring-white/15 shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-[#FEDC97]' : 'text-slate-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>
                            </svg>
                            <span>Role &amp; Izin</span>
                        </a>
                    @endcan
                </nav>
            </div>
        @endcanany
    </div>

    <div class="border-t border-slate-800/80 bg-[#011724]/90 p-3">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-tr from-[#28666E] to-[#7C9885] text-xs font-bold text-white shadow ring-1 ring-white/20">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold text-white leading-tight">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10px] text-slate-400 leading-tight mt-0.5">
                        {{ auth()->user()->hasRole('admin') ? 'Super Admin' : (auth()->user()->getRoleNames()->first() ?? 'User') }}{{ auth()->user()->department ? ' - Div. ' . auth()->user()->department->code : '' }}
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar dari sistem" class="rounded-lg p-1.5 text-slate-400 hover:bg-white/10 hover:text-rose-300 transition-colors cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
    </div>
</aside>
