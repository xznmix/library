<!DOCTYPE html>
<html lang="id" class="{{ session('dark-mode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kepala Pustaka - Perpustakaan SMAN 1 Tambang')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon.png') }}">

    {{-- CSS Includes --}}
    @include('kepala-pustaka.layouts.css')
    
    @stack('styles')
</head>

<body class="font-sans antialiased bg-brand-light">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('kepala-pustaka.layouts.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            @include('kepala-pustaka.layouts.topbar')

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-brand-light">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('kepala-pustaka.layouts.footer')
        </div>
    </div>

    {{-- ========== JAVASCRIPT SECTION ========== --}}
    @include('kepala-pustaka.layouts.js')
    
    @stack('scripts')
</body>
</html>