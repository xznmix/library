<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard Petugas')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <img src="{{ secure_asset('img/logo.jpg') }}">

    @include('petugas.layouts.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans transition-colors duration-200 overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar - fix dengan flex-shrink-0 --}}
        @include('petugas.layouts.sidebar')

        {{-- Main Content - scrollable --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Topbar --}}
            @include('petugas.layouts.topbar')

            {{-- Page Content - scrollable area --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 dark:bg-gray-900">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('petugas.layouts.footer')
        </div>
    </div>

    @include('petugas.layouts.js')
    {{-- <script>
    (function() {
        // Dark mode initialization - PAKSA DEFAULT TERANG
        function setTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('dark-mode', 'false');
            }
            
            // Update semua toggle icons
            updateToggleIcons();
        }

        function updateToggleIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            
            // Update topbar toggle
            const topbarToggle = document.getElementById('topbarDarkModeToggle');
            if (topbarToggle) {
                const sunIcon = topbarToggle.querySelector('.sun-icon');
                const moonIcon = topbarToggle.querySelector('.moon-icon');
                
                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
            }
            
            // Update settings toggle if exists
            const settingsToggle = document.getElementById('settingsDarkMode');
            if (settingsToggle) {
                const sunIcon = settingsToggle.querySelector('.sun-icon');
                const moonIcon = settingsToggle.querySelector('.moon-icon');
                
                if (sunIcon && moonIcon) {
                    if (isDark) {
                        sunIcon.classList.remove('hidden');
                        moonIcon.classList.add('hidden');
                    } else {
                        sunIcon.classList.add('hidden');
                        moonIcon.classList.remove('hidden');
                    }
                }
            }
        }

        // Load saved theme - DEFAULT TERANG
        const savedTheme = localStorage.getItem('dark-mode');
        
        // PERBAIKAN: Jika tidak ada saved theme, SET TERANG
        if (savedTheme === null) {
            // Tidak ada preferensi tersimpan → SET TERANG
            setTheme(false);
        } else if (savedTheme === 'true') {
            // User pernah memilih gelap
            setTheme(true);
        } else {
            // User pernah memilih terang
            setTheme(false);
        }

        // Add click listener to topbar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const topbarToggle = document.getElementById('topbarDarkModeToggle');
            if (topbarToggle) {
                topbarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isDark = !document.documentElement.classList.contains('dark');
                    setTheme(isDark);
                });
            }
            
            // Update icons after DOM is loaded
            updateToggleIcons();
        });
    })();
    </script> --}}
     @stack('scripts')
</body>
</html>