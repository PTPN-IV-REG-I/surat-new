<header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 lg:hidden" aria-label="Buka menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="hidden text-sm font-medium text-slate-500 lg:block">
        @yield('breadcrumb', 'Dashboard')
    </div>

    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="flex items-center gap-2 rounded-full text-sm">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 font-semibold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
            <span class="hidden text-left sm:block">
                <span class="block text-sm font-medium text-slate-900">{{ auth()->user()->name }}</span>
                <span class="block text-xs text-slate-500">{{ auth()->user()->getRoleNames()->first() ?? '-' }}</span>
            </span>
        </button>

        <div x-show="open" @click.outside="open = false" x-transition style="display: none;"
             class="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-slate-200 bg-white py-1 shadow-lg">
            <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Ganti Password</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-slate-700 hover:bg-slate-50">Logout</button>
            </form>
        </div>
    </div>
</header>
