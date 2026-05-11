<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Perpustakaan SMAN 1 Tambang')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ secure_asset('img/logo.jpg') }}">
    @include('admin.layouts.css')
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen">
        @include('admin.layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            @include('admin.layouts.topbar')

            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-brand-light">
                @yield('content')
            </main>

            @include('admin.layouts.footer')
        </div>
    </div>

    @include('admin.layouts.js')
</body>
</html>