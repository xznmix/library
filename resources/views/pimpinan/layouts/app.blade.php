<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pimpinan - Perpustakaan SMAN 1 Tambang')</title>
    <link rel="icon" href="{{ secure_asset('img/logo.jpg') }}">

    {{-- CSS Includes --}}
    @include('pimpinan.layouts.css')
    
    @stack('styles')
</head>

<body class="font-sans antialiased bg-brand-light">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('pimpinan.layouts.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            @include('pimpinan.layouts.topbar')

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-brand-light">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('pimpinan.layouts.footer')
        </div>
    </div>

    {{-- ========== JAVASCRIPT SECTION ========== --}}
    @include('pimpinan.layouts.js')
    
    @stack('scripts')
</body>
</html>