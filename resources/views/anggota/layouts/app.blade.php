<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard Anggota')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <img src="{{ secure_asset('img/logo.jpg') }}">

    @include('anggota.layouts.css')
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('anggota.layouts.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col">

            {{-- Topbar --}}
            @include('anggota.layouts.topbar')

            {{-- Page Content --}}
            <main class="p-4 md:p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('anggota.layouts.footer')

        </div>
    </div>

    @include('anggota.layouts.js')
    @stack('scripts')
</body>
</html>