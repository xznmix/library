<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OPAC - Online Public Access Catalog Perpustakaan SMAN 1 Tambang. Temukan koleksi buku dengan AI cerdas.">
    <meta name="keywords" content="perpustakaan, opac, katalog online, sman 1 tambang, buku, e-book, AI search">
    <meta name="author" content="Perpustakaan SMAN 1 Tambang">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'OPAC Perpustakaan') - SMAN 1 Tambang</title>
    
    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Warna Standar */
        .text-biru { color: #3B82F6; }
        .text-oren { color: #F97316; }
        .text-hijau { color: #10B981; }
        .text-hitam { color: #1F2937; }
        .bg-biru { background-color: #3B82F6; }
        .bg-oren { background-color: #F97316; }
        .bg-hijau { background-color: #10B981; }
        .hover\:bg-biru-dark:hover { background-color: #2563EB; }
        .hover\:bg-oren-dark:hover { background-color: #EA580C; }
        .border-biru { border-color: #3B82F6; }
        .border-oren { border-color: #F97316; }
        
        /* Navbar Scroll Effect */
        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body class="bg-white">

{{-- Navbar --}}
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-biru flex items-center justify-center shadow-lg transition-all group-hover:scale-105">
                    <img src="{{ asset('storage/logo.jpg') }}" alt="Logo SMAN 1 Tambang" class="w-8 h-8 md:w-10 md:h-10 rounded-lg object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'%233B82F6\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50\' y=\'67\' font-size=\'50\' text-anchor=\'middle\' fill=\'%23F97316\'%3E📚%3C/text%3E%3C/svg%3E'">
                </div>
                <div>
                    <span class="font-bold text-base md:text-lg text-biru">TAMBANG ILMU</span>
                    <span class="text-xs text-gray-500 block">Perpustakaan Digital</span>
                </div>
            </a>
            
            {{-- Desktop Menu --}}
            <nav class="hidden md:flex items-center gap-6">
                <a href="{{ route('home') }}" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Beranda</a>
                <a href="{{ route('opac.index') }}" class="nav-link text-oren font-semibold border-b-2 border-oren pb-1">OPAC</a>
                <a href="#koleksi" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Koleksi</a>
                <a href="#kontak" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Kontak</a>
            </nav>
            
            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-block text-hitam hover:text-oren font-medium transition-colors px-3 py-2">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="bg-biru hover:bg-biru-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-all transform hover:scale-105 shadow-md">
                        Daftar
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <span class="hidden md:block text-sm text-hitam">
                            Halo, <span class="font-semibold text-biru">{{ Auth::user()->name }}</span>
                        </span>
                        <a href="{{ route('dashboard') }}" class="bg-biru/10 text-biru px-4 py-2 rounded-full font-medium hover:bg-biru/20 transition-all flex items-center gap-2 text-sm">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="hidden sm:inline">Dashboard</span>
                        </a>
                    </div>
                @endguest
                
                {{-- Mobile Menu Button --}}
                <button id="menuToggle" class="md:hidden w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                    <i class="fas fa-bars text-hitam text-lg"></i>
                </button>
            </div>
        </div>
    </div>
    
    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden md:hidden bg-white border-t border-gray-100 py-4 px-4 shadow-lg">
        <div class="flex flex-col space-y-2">
            <a href="{{ route('home') }}" class="nav-link px-4 py-3 text-hitam hover:text-oren hover:bg-gray-50 rounded-lg transition-all">Beranda</a>
            <a href="{{ route('opac.index') }}" class="nav-link px-4 py-3 text-oren bg-oren/10 rounded-lg font-semibold">OPAC</a>
            <a href="#koleksi" class="nav-link px-4 py-3 text-hitam hover:text-oren hover:bg-gray-50 rounded-lg transition-all">Koleksi</a>
            <a href="#kontak" class="nav-link px-4 py-3 text-hitam hover:text-oren hover:bg-gray-50 rounded-lg transition-all">Kontak</a>
            
            @guest
                <div class="pt-3 mt-2 border-t border-gray-100">
                    <a href="{{ route('login') }}" class="block px-4 py-3 text-center text-hitam hover:text-oren hover:bg-gray-50 rounded-lg transition-all">Masuk</a>
                    <a href="{{ route('register') }}" class="block mt-2 px-4 py-3 text-center bg-biru text-white rounded-lg font-medium">Daftar</a>
                </div>
            @endguest
        </div>
    </div>
</header>

{{-- Main Content --}}
<main class="flex-1 pt-16">
    @yield('content')
</main>

{{-- Footer --}}
<footer id="kontak" class="bg-gray-900 text-white pt-12 pb-8 mt-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-biru flex items-center justify-center">
                        <img src="{{ asset('storage/logo.jpg') }}" alt="Logo" class="w-8 h-8 rounded object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'%233B82F6\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50\' y=\'67\' font-size=\'50\' text-anchor=\'middle\' fill=\'%23F97316\'%3E📚%3C/text%3E%3C/svg%3E'">
                    </div>
                    <div>
                        <span class="font-bold text-oren">TAMBANG ILMU</span>
                        <span class="text-xs text-gray-400 block">Perpustakaan Digital</span>
                    </div>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Perpustakaan digital SMAN 1 Tambang menyediakan akses mudah ke berbagai koleksi literasi.
                </p>
            </div>
            
            <div>
                <h4 class="font-semibold text-white mb-3">Tautan Cepat</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="{{ route('opac.index') }}" class="hover:text-oren transition">OPAC Pencarian</a></li>
                    <li><a href="#" class="hover:text-oren transition">Koleksi Digital</a></li>
                    <li><a href="#" class="hover:text-oren transition">FAQ</a></li>
                    <li><a href="#" class="hover:text-oren transition">Panduan</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-white mb-3">Kontak</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-start gap-2"><i class="fas fa-map-marker-alt text-oren mt-1"></i> Jl. Pekanbaru – Bangkinang KM.29, Tambang</li>
                    <li class="flex items-center gap-2"><i class="fab fa-whatsapp text-hijau"></i> +62 812-3456-7890</li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope text-oren"></i> perpus@sman1tambang.sch.id</li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-semibold text-white mb-3">Jam Layanan</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex justify-between"><span>Senin - Jumat</span><span class="text-white">07.00 - 16.00</span></li>
                    <li class="flex justify-between"><span>Sabtu</span><span class="text-white">08.00 - 12.00</span></li>
                    <li class="flex justify-between"><span>Minggu</span><span class="text-red-400">Tutup</span></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Perpustakaan SMAN 1 Tambang | <span class="text-oren">TAMBANG ILMU</span> - Mencerdaskan Generasi
        </div>
    </div>
</footer>

{{-- Scripts --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 800, once: true });

// Navbar Scroll Effect
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

// Mobile Menu Toggle
document.getElementById('menuToggle')?.addEventListener('click', function() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
});

// Close mobile menu on resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        document.getElementById('mobileMenu')?.classList.add('hidden');
    }
});
</script>

@stack('scripts')
</body>
</html>