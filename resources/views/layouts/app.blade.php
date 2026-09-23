<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') &middot; Surat PTPN</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800"
      x-data="{
          desktopSidebarOpen: localStorage.getItem('sidebar_expanded') !== 'false',
          mobileSidebarOpen: false,
          toggleSidebar() {
              if (window.innerWidth >= 1024) {
                  this.desktopSidebarOpen = !this.desktopSidebarOpen;
                  localStorage.setItem('sidebar_expanded', this.desktopSidebarOpen);
              } else {
                  this.mobileSidebarOpen = !this.mobileSidebarOpen;
              }
          }
      }">
    <div class="flex min-h-screen bg-slate-50">
        @include('components.sidebar')

        <div class="flex min-w-0 flex-1 flex-col bg-slate-50 transition-all duration-300 ease-in-out">
            @include('components.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Global Reusable Confirmation Modal --}}
    <x-confirm-modal />

    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                toastr.success(@json(session('status')));
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                toastr.error(@json($errors->first()));
            });
        </script>
    @endif
</body>
</html>
