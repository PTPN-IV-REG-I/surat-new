<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') &middot; Surat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    <div class="flex h-full">
        @include('components.sidebar')

        <div class="flex flex-1 flex-col overflow-hidden">
            @include('components.topbar')

            <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

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
