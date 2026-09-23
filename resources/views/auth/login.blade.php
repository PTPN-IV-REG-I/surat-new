<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk &middot; Surat PTPN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen w-screen overflow-hidden bg-white font-sans antialiased text-slate-800" x-data="{ showHelpModal: false }">

    <div class="flex w-full h-full min-h-0 flex-col lg:flex-row overflow-hidden">

        <div class="relative hidden w-full lg:flex lg:w-[46%] xl:w-[44%] 2xl:w-[42%] flex-col justify-between overflow-hidden bg-[#033F63] p-8 lg:p-12 xl:p-14 2xl:p-16 text-white shrink-0">
            <div class="pointer-events-none absolute inset-0 opacity-15">
                <svg class="h-full w-full" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="300" cy="300" r="260" stroke="#FEDC97" stroke-width="1.5" stroke-dasharray="6 6" />
                    <circle cx="300" cy="300" r="200" stroke="#FFFFFF" stroke-width="1" />
                    <circle cx="300" cy="300" r="140" stroke="#28666E" stroke-width="1.5" stroke-dasharray="4 4" />
                    <path d="M300 80 L320 300 L300 520 L280 300 Z" fill="#FEDC97" opacity="0.3" />
                    <path d="M80 300 L300 320 L520 300 L300 280 Z" fill="#FEDC97" opacity="0.3" />
                </svg>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#28666E] to-[#022B44] text-white shadow-md shadow-black/25 ring-1 ring-white/15">
                    <svg class="h-6 w-6 text-[#FEDC97]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-base font-extrabold tracking-wider text-white leading-tight">SURAT PTPN</span>
                    <span class="text-[10px] font-semibold tracking-widest text-[#7C9885] uppercase leading-tight mt-0.5">Tata Persuratan &amp; Arsip</span>
                </div>
            </div>

            <div class="relative z-10 flex flex-col justify-center my-auto py-6">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-medium text-white/90 backdrop-blur-xs">
                        <svg class="h-3.5 w-3.5 text-[#FEDC97]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Sistem Agenda &amp; Disposisi Surat Kedinasan
                    </span>
                </div>

                <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white xl:text-4xl">Surat PTPN</h1>
                <p class="mt-2 text-sm font-normal text-white/85 leading-relaxed">Pencatatan agenda, tracking lembar disposisi, dan monitoring tindak lanjut surat</p>
                <div class="mt-3 h-1 w-16 rounded-full bg-[#FEDC97]"></div>

                <div class="mt-6 space-y-3 max-w-lg">
                    <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/10 p-3.5 backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#28666E]/50 text-[#FEDC97]">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-white">Buku Agenda Surat Masuk &amp; Keluar</h3>
                            <p class="mt-0.5 text-[11px] text-white/75 leading-relaxed">Pencatatan nomor agenda, tanggal terima, dan klasifikasi surat seluruh unit kerja</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/10 p-3.5 backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#28666E]/50 text-[#FEDC97]">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-white">Cetak &amp; Tracking Lembar Disposisi</h3>
                            <p class="mt-0.5 text-[11px] text-white/75 leading-relaxed">Format cetak lembar disposisi resmi untuk paraf/tanda tangan basah pimpinan</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/10 p-3.5 backdrop-blur-md transition hover:bg-white/15">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#28666E]/50 text-[#FEDC97]">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-semibold text-white">Integrasi Regional, Bagian &amp; Kebun/Unit</h3>
                            <p class="mt-0.5 text-[11px] text-white/75 leading-relaxed">Monitoring arsip persuratan Kantor Regional, Bagian, Kebun, PKS, dan Unit Kerja</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 pt-4 text-[11px] text-white/60">
                &copy; 2026 PT Perkebunan Nusantara. Hak Cipta Dilindungi.
            </div>
        </div>

        <div class="flex flex-1 flex-col justify-between bg-white px-6 py-8 sm:px-12 lg:px-14 xl:px-20 overflow-y-auto min-h-0">
            {{-- Mobile-only Brand Header --}}
            <div class="flex lg:hidden items-center gap-3 pb-4 border-b border-slate-100">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#28666E] to-[#022B44] text-white shadow-xs">
                    <svg class="h-5 w-5 text-[#FEDC97]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-extrabold tracking-wider text-slate-900 block leading-tight">SURAT PTPN</span>
                    <span class="text-[10px] font-semibold tracking-widest text-[#28666E] uppercase block leading-tight">Tata Persuratan &amp; Arsip</span>
                </div>
            </div>

            <div class="hidden lg:block"></div>

            <div class="mx-auto w-full max-w-md py-4 my-auto">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-[#28666E] uppercase">AUTENTIKASI PEGAWAI</span>
                </div>

                <h2 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Masuk ke Akun Anda</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan kredensial resmi kepegawaian Anda untuk mengakses sistem persuratan.</p>

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl border border-red-200 bg-[#FEE2E2] p-3.5 text-left shadow-xs">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 text-red-600 mt-0.5">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-[#991B1B]">Akses Ditolak</h4>
                                <p class="mt-0.5 text-xs text-[#991B1B]/90 leading-relaxed">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('throttle_seconds'))
                    <div class="mt-3 rounded-2xl border border-amber-200 bg-[#FEF3C7] p-3.5 text-left shadow-xs" x-data="{ seconds: {{ session('throttle_seconds', 60) }} }" x-init="setInterval(() => { if (seconds > 0) seconds-- }, 1000)">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="h-4.5 w-4.5 text-[#92400E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-bold text-[#92400E]">Pembatasan Keamanan</span>
                            </div>
                            <span class="rounded-md bg-[#FDE68A] px-2 py-0.5 text-[11px] font-bold text-[#92400E]" x-text="seconds + 's'"></span>
                        </div>
                        <p class="mt-1 text-xs text-[#92400E]/90">Terlalu banyak percobaan masuk. Silakan tunggu sebelum mencoba kembali.</p>
                        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-[#FDE68A]">
                            <div class="h-full bg-[#033F63] transition-all duration-1000" :style="'width: ' + ((seconds / 60) * 100) + '%'"></div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-3.5" x-data="{ showPassword: false, submitting: false }" @submit="submitting = true">
                    @csrf

                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700">Username</label>
                        <div class="relative mt-1.5 rounded-xl border border-slate-200 bg-slate-50/60 shadow-xs transition focus-within:border-[#033F63] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#033F63]/15">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                                   placeholder="Contoh: agendaris.ptpn / NIK"
                                   class="block w-full h-11 rounded-xl bg-transparent pl-10 pr-4 text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                        <div class="relative mt-1.5 rounded-xl border border-slate-200 bg-slate-50/60 shadow-xs transition focus-within:border-[#033F63] focus-within:bg-white focus-within:ring-2 focus-within:ring-[#033F63]/15">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   placeholder="Masukkan kata sandi kedinasan Anda"
                                   class="block w-full h-11 rounded-xl bg-transparent pl-10 pr-11 text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:outline-none">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                <svg x-show="!showPassword" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-0.5">
                        <button type="button" @click="showHelpModal = true" class="text-xs font-semibold text-[#033F63] hover:underline cursor-pointer">
                            Lupa Password?
                        </button>
                    </div>

                    <button type="submit" :disabled="submitting"
                            class="mt-2 flex w-full h-11 items-center justify-center gap-2 rounded-xl bg-[#033F63] px-6 text-xs font-bold text-white shadow-xs transition hover:bg-[#022B44] active:bg-[#011724] focus:outline-none focus:ring-2 focus:ring-[#033F63]/30 disabled:opacity-60 cursor-pointer select-none">
                        <span x-show="!submitting" class="inline-flex items-center gap-2">
                            Masuk
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                        <span x-show="submitting" class="inline-flex items-center gap-2" style="display: none;">
                            <svg class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memverifikasi Kredensial...
                        </span>
                    </button>
                </form>

                <div class="mt-5 text-center text-[11px] text-slate-500">
                    Butuh bantuan teknis? Hubungi <a href="https://ptpn3.co.id/helpdesk/tiket/create" target="_blank" rel="noopener noreferrer" class="font-semibold text-[#033F63] hover:underline inline-flex items-center gap-1">Tim IT Helpdesk PTPN IV Regional I <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                </div>
            </div>

            <div class="hidden lg:block py-1"></div>
        </div>
    </div>

    {{-- Modal Lupa Password / Bantuan --}}
    <div x-show="showHelpModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200/80" @click.outside="showHelpModal = false">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#033F63]/10 text-[#033F63] ring-1 ring-[#033F63]/20">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Bantuan Lupa Password</h3>
                    <p class="text-xs text-slate-500">Prosedur Keamanan Kredensial PTPN</p>
                </div>
            </div>

            <div class="mt-4 space-y-2.5 rounded-xl bg-slate-50 p-4 text-xs text-slate-600 leading-relaxed border border-slate-100">
                <p>Untuk menjaga kerahasiaan naskah dinas korporasi, reset kata sandi tidak dilakukan secara mandiri melalui email.</p>
                <p>Silakan hubungi <strong>Administrator Sistem Surat</strong> di unit kerja Anda atau ajukan tiket ke Helpdesk TI untuk mendapatkan kata sandi baru.</p>
                <div class="pt-1 font-semibold text-[#033F63]">
                    Portal Helpdesk: <a href="https://ptpn3.co.id/helpdesk/tiket/create" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#022B44]">ptpn3.co.id/helpdesk/tiket/create</a>
                </div>
            </div>

            <div class="mt-5 flex flex-col gap-2">
                <a href="https://ptpn3.co.id/helpdesk/tiket/create" target="_blank" rel="noopener noreferrer"
                   class="flex w-full h-10 items-center justify-center gap-2 rounded-xl bg-[#033F63] text-center text-xs font-bold text-white hover:bg-[#022B44] transition shadow-xs">
                    <span>Buat Tiket Bantuan Helpdesk</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button type="button" @click="showHelpModal = false"
                        class="w-full h-9 rounded-xl text-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</body>
</html>
