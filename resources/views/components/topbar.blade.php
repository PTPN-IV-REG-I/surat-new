<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-slate-200/90 bg-white/95 backdrop-blur-xs px-4 sm:px-6">
    <div class="flex items-center gap-3">
        <button @click="toggleSidebar()"
                class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200/80 bg-slate-50/70 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none cursor-pointer"
                :title="desktopSidebarOpen ? 'Sembunyikan Sidebar' : 'Tampilkan Sidebar'"
                aria-label="Toggle Menu Navigasi">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <nav class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="transition-colors hover:text-[#033F63]">Beranda</a>
            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-semibold text-slate-800">@yield('breadcrumb', 'Dashboard Tata Kelola Persuratan')</span>
        </nav>
    </div>

    <div class="hidden md:block flex-1 max-w-md mx-6">
        <form method="GET" action="{{ route('letters.index') }}" class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor surat, perihal, pengirim..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2 pl-9 pr-4 text-xs text-slate-700 placeholder-slate-400 transition hover:bg-white focus:bg-white focus:border-[#033F63] focus:outline-none focus:ring-1 focus:ring-[#033F63]">
        </form>
    </div>

    <div class="flex items-center gap-3">
        <div x-data="{ notifOpen: false }" class="relative">
            <button @click="notifOpen = !notifOpen"
                    class="relative flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200/80 bg-slate-50/50 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none cursor-pointer"
                    aria-label="Notifikasi">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white">3</span>
            </button>

            <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition style="display: none;"
                 class="absolute right-0 z-50 mt-2 w-80 rounded-2xl border border-slate-200 bg-white p-3 shadow-xl ring-1 ring-black/5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2 px-1">
                    <span class="text-xs font-bold text-slate-800">Notifikasi Persuratan</span>
                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">3 Menunggu</span>
                </div>
                <div class="mt-2 divide-y divide-slate-100 text-xs text-slate-600">
                    <a href="{{ route('letters.index') }}" class="block py-2 px-2 hover:bg-slate-50 rounded-lg transition">
                        <p class="font-semibold text-slate-800">14 Surat Dinas Baru</p>
                        <p class="text-[11px] text-slate-500">Memerlukan telaah lembar disposisi pimpinan.</p>
                    </a>
                    <a href="{{ route('reports.follow-up') }}" class="block py-2 px-2 hover:bg-slate-50 rounded-lg transition">
                        <p class="font-semibold text-slate-800">38 Tindak Lanjut Aktif</p>
                        <p class="text-[11px] text-slate-500">Monitoring tenggat waktu instruksi bagian/kebun.</p>
                    </a>
                    <div class="pt-2 px-2 text-center">
                        <a href="{{ route('letters.index') }}" class="text-[11px] font-semibold text-[#033F63] hover:underline">Lihat Semua Arsip Surat &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 rounded-xl border border-slate-200/80 bg-slate-50/60 p-1.5 pr-2.5 transition hover:bg-slate-100/80 focus:outline-none cursor-pointer">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-linear-to-tr from-[#28666E] to-[#7C9885] font-bold text-xs text-white shadow-xs ring-1 ring-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="hidden text-left sm:block">
                    <span class="block text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                    <span class="block text-[10px] font-medium text-slate-500 leading-tight">
                        {{ auth()->user()->hasRole('admin') ? 'Super Admin Persuratan' : (auth()->user()->getRoleNames()->first() ?? 'Pengguna') }}
                    </span>
                </div>
                <svg class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
                 class="absolute right-0 z-50 mt-2 w-64 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl ring-1 ring-black/5">
                <div class="rounded-xl bg-slate-50 p-3 text-xs">
                    <p class="font-bold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">ID: {{ auth()->user()->username }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <span class="rounded-md bg-[#033F63] px-2 py-0.5 text-[10px] font-semibold text-white">
                            {{ auth()->user()->hasRole('admin') ? 'Super Admin' : (auth()->user()->getRoleNames()->first() ?? 'User') }}
                        </span>
                        @if (auth()->user()->department)
                            <span class="rounded-md bg-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-700">
                                {{ auth()->user()->department->code }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-1 space-y-0.5 text-xs">
                    <a href="https://ptpn3.co.id/helpdesk/tiket/create" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-between rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-[#033F63]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Bantuan IT Helpdesk
                        </span>
                        <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left font-medium text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
