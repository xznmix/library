<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Perpustakaan Digital SMAN 1 Tambang</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AOS Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
            background: #FFFFFF;
        }
        
        /* Warna Standar: Oren, Biru, Hijau, Hitam, Putih - TANPA GRADASI */
        :root {
            --oren: #F97316;
            --oren-dark: #EA580C;
            --biru: #3B82F6;
            --biru-dark: #2563EB;
            --hijau: #10B981;
            --hitam: #1F2937;
            --putih: #FFFFFF;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #F3F4F6;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--biru);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--biru-dark);
        }
        
        /* Background pattern */
        .bg-pattern {
            position: relative;
            background: var(--putih);
        }
        
        .bg-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext x='50%25' y='50%25' font-size='40' text-anchor='middle' dominant-baseline='middle' opacity='0.03'%3E🏫%3C/text%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 60px;
            pointer-events: none;
            z-index: 0;
        }
        
        .bg-pattern > * {
            position: relative;
            z-index: 1;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        /* Navbar transition */
        .navbar-scrolled {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        /* Card style */
        .card-elegant {
            background: var(--putih);
            border-radius: 20px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card-elegant:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 40px -20px rgba(0, 0, 0, 0.15);
            border-color: var(--oren);
        }
        
        /* Button styles - TANPA GRADASI */
        .btn-primary {
            background: var(--biru);
            color: var(--putih);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(59,130,246,0.5);
            background: var(--biru-dark);
        }
        
        .btn-secondary {
            background: var(--oren);
            color: var(--putih);
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(249,115,22,0.3);
            background: var(--oren-dark);
        }
        
        /* Feature card - TANPA GRADASI */
        .feature-card {
            background: var(--putih);
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: var(--oren);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--biru);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .animate-float {
            animation: float 4s ease-in-out infinite;
        }
        
        /* Hero section overlay */
        .hero-overlay {
            background: radial-gradient(circle at 10% 20%, rgba(59,130,246,0.05), rgba(249,115,22,0.02));
        }
        
        /* AI Search Bar */
        .ai-search-container {
            background: var(--putih);
            border-radius: 60px;
            padding: 5px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .ai-search-container:focus-within {
            box-shadow: 0 10px 40px rgba(249,115,22,0.2);
            transform: scale(1.02);
        }
        
        .ai-search-input {
            border: none;
            background: transparent;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            width: 100%;
            outline: none;
        }
        
        /* Notification */
        .notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--putih);
            border-radius: 20px;
            max-width: 90%;
            width: 500px;
            max-height: 85vh;
            overflow-y: auto;
            position: relative;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
            z-index: 10;
        }
        
        .close-modal:hover {
            color: var(--oren);
        }
        
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px auto;
            }
        }
        
        /* Warna solid utility */
        .bg-biru { background-color: #3B82F6; }
        .bg-biru-dark { background-color: #2563EB; }
        .bg-oren { background-color: #F97316; }
        .bg-oren-dark { background-color: #EA580C; }
        .bg-hijau { background-color: #10B981; }
        
        .text-biru { color: #3B82F6; }
        .text-oren { color: #F97316; }
        .text-hijau { color: #10B981; }
        .text-hitam { color: #1F2937; }
        
        .hover\:bg-biru-dark:hover { background-color: #2563EB; }
        .hover\:bg-oren-dark:hover { background-color: #EA580C; }
        .hover\:text-biru:hover { color: #3B82F6; }
        .hover\:text-oren:hover { color: #F97316; }
    </style>
</head>
<body class="bg-pattern">

<!-- ==================== NAVBAR ==================== -->
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <a href="#beranda" class="flex items-center gap-3 group cursor-pointer">
                <div class="w-12 h-12 rounded-xl bg-biru flex items-center justify-center shadow-lg transition-all group-hover:scale-105">
                    <img src="{{ asset('storage/logo.jpg') }}" alt="Logo SMAN 1 Tambang" class="w-10 h-10 rounded-lg object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'%233B82F6\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50\' y=\'67\' font-size=\'50\' text-anchor=\'middle\' fill=\'%23F97316\'%3E📚%3C/text%3E%3C/svg%3E'">
                </div>
                <div>
                    <span class="font-bold text-lg text-biru">TAMBANG ILMU</span>
                    <span class="text-xs text-gray-500 block">Perpustakaan SMAN 1 Tambang</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-8">
                <a href="#beranda" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Beranda</a>
                <a href="#fitur" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Fitur</a>
                <a href="#koleksi" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Koleksi</a>
                <a href="#statistik" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Statistik</a>
                <a href="#kontak" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Kontak</a>
            </nav>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-block text-hitam hover:text-oren font-medium transition-colors px-4 py-2">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-biru hover:bg-biru-dark text-white px-5 py-2 rounded-full text-sm font-medium transition-all transform hover:scale-105">
                    Daftar
                </a>
                <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-hitam text-xl"></i>
                </button>
            </div>
        </div>
        
        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 flex flex-col gap-3">
            <a href="#beranda" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Beranda</a>
            <a href="#fitur" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Fitur</a>
            <a href="#koleksi" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Koleksi</a>
            <a href="#statistik" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Statistik</a>
            <a href="#kontak" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Kontak</a>
        </div>
    </div>
</header>

<!-- ==================== HERO SECTION ==================== -->
<section id="beranda" class="min-h-screen flex items-center relative overflow-hidden pt-20">
    <div class="absolute inset-0 hero-overlay"></div>
    <div class="absolute top-20 right-0 w-72 h-72 bg-oren rounded-full filter blur-3xl opacity-10 animate-float"></div>
    <div class="absolute bottom-20 left-0 w-96 h-96 bg-biru rounded-full filter blur-3xl opacity-10 animate-float" style="animation-delay: 2s;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-up" data-aos-duration="1000">
                <div class="inline-flex items-center gap-2 bg-oren/10 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-graduation-cap text-oren text-sm"></i>
                    <span class="text-sm font-medium text-biru">SMAN 1 TAMBANG</span>
                </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold leading-tight mb-6">
                        <span class="text-biru">Perpustakaan Digital</span>
                        <br>
                        <span class="text-hitam">SMAN 1 Tambang</span>
                    </h1>
                <p class="text-hitam text-lg mb-8 leading-relaxed">
                    Akses ribuan koleksi buku, modul pembelajaran, dan ebook dari mana saja. 
                    Bergabunglah dengan komunitas pembelajar di Perpustakaan Digital SMAN 1 Tambang.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#koleksi" class="btn-primary px-8 py-3 rounded-full font-medium text-center inline-flex items-center justify-center gap-2">
                        <i class="fas fa-book-open"></i> Jelajahi Koleksi
                    </a>
                    <a href="{{ route('register') }}" class="btn-secondary px-8 py-3 rounded-full font-medium text-center inline-flex items-center justify-center gap-2">
                        <i class="fas fa-user-plus"></i> Daftar Gratis
                    </a>
                </div>
            </div>
            
            <div class="relative" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('img/profil.jpg') }}" 
                         alt="Perpustakaan SMAN 1 Tambang"
                         class="w-full h-auto object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
                <div class="absolute -bottom-5 -left-5 w-32 h-32 bg-oren rounded-2xl opacity-20 blur-2xl"></div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FITUR UNGGULAN SECTION ==================== -->
<section id="fitur" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-sm uppercase tracking-wider">Layanan Unggulan</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-hitam mt-2 mb-4">Fitur Modern Perpustakaan Digital</h2>
            <p class="text-hitam max-w-2xl mx-auto">Kami menghadirkan berbagai fitur canggih untuk pengalaman membaca yang lebih baik</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fab fa-whatsapp text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-3">Notifikasi WhatsApp</h3>
                <p class="text-hitam text-sm">Dapatkan notifikasi keterlambatan pengembalian dan informasi terbaru via WhatsApp</p>
                <span class="inline-block mt-4 text-hijau text-sm font-semibold">Tersedia 24/7</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200" onclick="openAISearch()">
                <div class="feature-icon">
                    <i class="fas fa-robot text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-3">AI Pencarian Cerdas</h3>
                <p class="text-hitam text-sm">Temukan buku rekomendasi dengan teknologi AI yang memahami kebutuhan Anda</p>
                <span class="inline-block mt-4 text-oren text-sm font-semibold">Mudah Digunakan</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-3">DRM Protection</h3>
                <p class="text-hitam text-sm">Keamanan konten digital terjamin dengan sistem DRM canggih</p>
                <span class="inline-block mt-4 text-oren text-sm font-semibold">Aman & Terpercaya</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-home text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-3">Booking dari Rumah</h3>
                <p class="text-hitam text-sm">Pesan buku favorit dari rumah dan ambil di perpustakaan</p>
                <span class="inline-block mt-4 text-oren text-sm font-semibold">Tanpa Antri</span>
            </div>
        </div>
    </div>
</section>

<!-- ==================== KOLEKSI SECTION ==================== -->
<section id="koleksi" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-sm uppercase tracking-wider">Koleksi Kami</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-hitam mt-2 mb-4">Jelajahi Koleksi Perpustakaan</h2>
            <p class="text-hitam max-w-2xl mx-auto">Beragam koleksi tersedia untuk mendukung proses belajar mengajar</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Buku Cetak -->
            <a href="{{ route('opac.index') }}?jenis=buku_cetak" class="card-elegant p-6 block" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-biru rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-2">Buku Cetak</h3>
                <p class="text-hitam text-sm mb-3">Koleksi buku fisik berbagai mata pelajaran dan literatur</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren"></i>
                </div>
            </a>
            
            <!-- Ebook Digital -->
            <a href="{{ route('opac.index') }}?jenis=ebook" class="card-elegant p-6 block" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-oren rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-tablet-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-2">Ebook Berlisensi dan Free Download</h3>
                <p class="text-hitam text-sm mb-3">Buku digital tersedia 2 versi, yang bisa dipinjam berlisensi dan free download</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren"></i>
                </div>
            </a>
            
            <!-- Modul Pembelajaran -->
            <a href="{{ route('opac.index') }}?jenis=modul" class="card-elegant p-6 block" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 bg-biru rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-file-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-2">Bank Soal</h3>
                <p class="text-hitam text-sm mb-3">Kumpulan bank soal free akses untuk mendukung pemahaman siswa</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren"></i>
                </div>
            </a>
            
            <!-- Modul -->
            <a href="{{ route('opac.index') }}?jenis=jurnal" class="card-elegant p-6 block" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 bg-oren rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-newspaper text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-hitam mb-2">Modul</h3>
                <p class="text-hitam text-sm mb-3">Modul ajar yang digunakan untuk mendukung pembelajaran</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren"></i>
                </div>
            </a>
        </div>
        
        <!-- OPAC Button -->
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="{{ route('opac.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-white border-2 border-oren text-oren rounded-full font-medium hover:bg-oren hover:text-white transition-all">
                <i class="fas fa-search"></i> Cari di OPAC (Online Public Access Catalog) <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- ==================== AI SEARCH MODAL ==================== -->
<div id="modal-ai-search" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-biru rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-robot text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-hitam">AI Pencarian Cerdas</h2>
                <p class="text-hitam mt-2">Tanyakan apapun tentang koleksi perpustakaan kami</p>
            </div>
            
            <div class="mb-4">
                <div class="ai-search-container">
                    <div class="flex items-center">
                        <i class="fas fa-robot text-oren ml-4"></i>
                        <input type="text" id="ai-search-input" placeholder="Contoh: Rekomendasikan buku tentang pemrograman untuk pemula..." class="ai-search-input">
                        <button id="ai-search-btn" class="btn-primary px-6 py-3 rounded-full m-1">
                            <i class="fas fa-paper-plane"></i> Kirim
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="ai-result" class="mt-6 p-4 bg-gray-50 rounded-xl hidden">
                <div class="flex items-start gap-3">
                    <i class="fas fa-lightbulb text-oren text-xl mt-1"></i>
                    <div>
                        <h4 class="font-bold text-hitam mb-2">Rekomendasi AI:</h4>
                        <p id="ai-response" class="text-hitam text-sm"></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-center text-xs text-gray-400">
                <i class="fas fa-microphone"></i> Fitur AI didukung oleh teknologi pemrosesan bahasa alami
            </div>
        </div>
    </div>
</div>

<!-- ==================== BOOKING MODAL ==================== -->
<div id="modal-booking" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-biru rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-home text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-hitam">Booking Buku dari Rumah</h2>
                <p class="text-hitam mt-2">Pesan buku favorit Anda dan ambil di perpustakaan</p>
            </div>
            
            <form id="booking-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-hitam mb-2">Cari Buku</label>
                    <div class="flex gap-2">
                        <input type="text" id="booking-search" placeholder="Masukkan judul buku..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-oren">
                        <button type="button" class="btn-primary px-4 py-2 rounded-lg" onclick="searchBook()">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
                
                <div id="book-result" class="hidden">
                    <label class="block text-sm font-medium text-hitam mb-2">Hasil Pencarian</label>
                    <div id="book-list" class="space-y-2 max-h-60 overflow-y-auto"></div>
                </div>
                
                <div class="text-center text-sm text-hitam">
                    <i class="fas fa-info-circle text-oren"></i>
                    Setelah booking, Anda memiliki waktu 1x24 jam untuk mengambil buku
                </div>
                
                <div class="flex justify-center">
                    <a href="{{ route('login') }}" class="btn-primary px-6 py-2 rounded-full">
                        Login untuk Booking
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================== STATISTIK SECTION ==================== -->
<section id="statistik" class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-biru"></div>
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ctext x=\'50%25\' y=\'50%25\' font-size=\'40\' text-anchor=\'middle\' dominant-baseline=\'middle\' fill=\'white\'%3E📚%3C/text%3E%3C/svg%3E'); background-repeat: repeat; background-size: 40px;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-sm uppercase tracking-wider">Perpustakaan dalam Angka</span>
            <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2">Fakta & Capaian Kami</h2>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <div class="text-center p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-3xl text-oren"></i>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">500+</div>
                <p class="text-white/80 font-medium text-sm md:text-base">Koleksi Buku Cetak</p>
            </div>
            
            <div class="text-center p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-tablet-alt text-3xl text-oren"></i>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">100+</div>
                <p class="text-white/80 font-medium text-sm md:text-base">Koleksi Ebook</p>
            </div>
            
            <div class="text-center p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-3xl text-oren"></i>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">400+</div>
                <p class="text-white/80 font-medium text-sm md:text-base">Anggota Aktif</p>
            </div>
            
            <div class="text-center p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="400">
                <div class="w-20 h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-check text-3xl text-oren"></i>
                </div>
                <div class="text-4xl md:text-5xl font-bold text-white mb-2">300+</div>
                <p class="text-white/80 font-medium text-sm md:text-base">Transaksi per Bulan</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer id="kontak" class="bg-white border-t border-gray-100 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <div data-aos="fade-up">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-biru flex items-center justify-center">
                        <img src="{{ asset('storage/logo.jpg') }}" alt="Logo" class="w-8 h-8 rounded object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'%233B82F6\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50\' y=\'67\' font-size=\'50\' text-anchor=\'middle\' fill=\'%23F97316\'%3E🏫%3C/text%3E%3C/svg%3E'">
                    </div>
                    <div>
                        <span class="font-bold text-biru">TAMBANG ILMU</span>
                        <span class="text-xs text-gray-500 block">Perpustakaan Digital</span>
                    </div>
                </div>
                <p class="text-hitam text-sm leading-relaxed">
                    Perpustakaan digital SMAN 1 Tambang menyediakan akses mudah ke berbagai koleksi literasi untuk mendukung kegiatan belajar mengajar.
                </p>
                <div class="flex gap-3 mt-4">
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-hijau hover:text-white transition-colors">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-oren hover:text-white transition-colors">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-biru hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-red-600 hover:text-white transition-colors">
                        <i class="fab fa-youtube text-sm"></i>
                    </a>
                </div>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="100">
                <h4 class="font-bold text-hitam mb-4">Tentang Kami</h4>
                <ul class="space-y-2 text-sm text-hitam">
                    <li><a href="#beranda" class="hover:text-oren transition-colors">Beranda</a></li>
                    <li><a href="#fitur" class="hover:text-oren transition-colors">Fitur</a></li>
                    <li><a href="#koleksi" class="hover:text-oren transition-colors">Koleksi</a></li>
                    <li><a href="#statistik" class="hover:text-oren transition-colors">Statistik</a></li>
                </ul>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="200">
                <h4 class="font-bold text-hitam mb-4">Layanan</h4>
                <ul class="space-y-2 text-sm text-hitam">
                    <li><a href="#" onclick="openBookingModal()" class="hover:text-oren transition-colors">Booking Buku</a></li>
                    <li><a href="#" onclick="openAISearch()" class="hover:text-oren transition-colors">AI Pencarian</a></li>
                    <li><a href="{{ route('opac.index') }}" class="hover:text-oren transition-colors">OPAC</a></li>
                    <li><a href="#" class="hover:text-oren transition-colors">E-Learning</a></li>
                </ul>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="300">
                <h4 class="font-bold text-hitam mb-4">Hubungi Kami</h4>
                <ul class="space-y-3 text-sm text-hitam">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-oren mt-1"></i>
                        <span>Jl. Pekanbaru – Bangkinang KM.29, Tambang, Kampar</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-hijau"></i>
                        <span>+62 812-3456-7890</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope text-oren"></i>
                        <span>perpustakaan@sman1tambang.sch.id</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-clock text-oren"></i>
                        <span>Senin - Jumat: 07.00 - 16.00</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-8 text-center">
            <p class="text-hitam text-sm">
                &copy; <span id="current-year"></span> Perpustakaan SMAN 1 Tambang | 
                <span class="text-oren">TAMBANG ILMU</span> - Mencerdaskan Generasi
            </p>
        </div>
    </div>
</footer>

<!-- ==================== NOTIFICATION CONTAINER ==================== -->
<div id="notification-container" class="fixed bottom-5 right-5 z-50 space-y-2"></div>

<!-- ==================== SCRIPTS ==================== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ once: true, duration: 800 });

function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#10B981' : (type === 'warning' ? '#F97316' : '#3B82F6');
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle');
    
    notification.className = `notification p-4 rounded-xl shadow-lg flex items-center gap-3 text-white`;
    notification.style.backgroundColor = bgColor;
    notification.innerHTML = `<i class="fas ${icon}"></i><span class="text-sm">${message}</span>`;
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

function openAISearch() {
    const modal = document.getElementById('modal-ai-search');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function openBookingModal() {
    const modal = document.getElementById('modal-booking');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', function() {
        closeModal(this.closest('.modal'));
    });
});

document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this);
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal.active').forEach(closeModal);
    }
});

document.getElementById('ai-search-btn')?.addEventListener('click', function() {
    const query = document.getElementById('ai-search-input').value.trim();
    if (!query) {
        showNotification('Masukkan pertanyaan terlebih dahulu!', 'warning');
        return;
    }
    
    const resultDiv = document.getElementById('ai-result');
    const responseDiv = document.getElementById('ai-response');
    
    resultDiv.classList.remove('hidden');
    responseDiv.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> AI sedang mencari rekomendasi...';
    
    setTimeout(() => {
        const responses = [
            `Berdasarkan preferensi Anda, saya merekomendasikan buku <strong>"Pemrograman Web dengan Laravel"</strong> oleh Sandhika Galih.`,
            `Saya menemukan beberapa buku yang sesuai: <strong>"Matematika SMA Kelas X"</strong>, <strong>"Fisika Dasar"</strong>, dan <strong>"Kimia untuk SMA"</strong>.`,
            `Rekomendasi untuk Anda: <strong>"English for Nusantara"</strong> untuk meningkatkan kemampuan bahasa Inggris.`,
            `Buku populer minggu ini: <strong>"Artificial Intelligence: Pendekatan Modern"</strong> oleh Stuart Russell.`
        ];
        responseDiv.innerHTML = responses[Math.floor(Math.random() * responses.length)];
        showNotification('AI telah menemukan rekomendasi untuk Anda!', 'success');
    }, 1500);
});

function searchBook() {
    const searchTerm = document.getElementById('booking-search').value.trim();
    if (!searchTerm) {
        showNotification('Masukkan judul buku yang ingin dicari!', 'warning');
        return;
    }
    
    const bookList = document.getElementById('book-list');
    const bookResult = document.getElementById('book-result');
    
    bookResult.classList.remove('hidden');
    bookList.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-pulse"></i> Mencari buku...</div>';
    
    setTimeout(() => {
        const books = [
            { title: 'Matematika SMA/MA Kelas X', author: 'Dr. Sukino, M.Si', available: true },
            { title: 'Fisika Dasar untuk SMA/MA', author: 'Prof. Dr. Yohanes Surya', available: true },
            { title: 'Bahasa Inggris: English for Nusantara', author: 'Dr. Utami Widiati', available: false },
            { title: 'Kimia untuk SMA/MA Kelas XI', author: 'Michael Purba, Ph.D', available: true }
        ];
        
        bookList.innerHTML = books.map(book => `
            <div class="p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                <div>
                    <h4 class="font-medium text-hitam">${book.title}</h4>
                    <p class="text-xs text-gray-500">${book.author}</p>
                    <span class="text-xs ${book.available ? 'text-hijau' : 'text-red-500'}">
                        ${book.available ? '● Tersedia' : '● Dipinjam'}
                    </span>
                </div>
                ${book.available ? `
                    <button onclick="showNotification('Silakan login untuk melanjutkan booking!', 'warning')" 
                            class="btn-primary text-xs px-3 py-1 rounded-full">
                        Booking
                    </button>
                ` : ''}
            </div>
        `).join('');
    }, 800);
}

window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

const menuToggle = document.getElementById('menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');

if (menuToggle) {
    menuToggle.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
}

document.querySelectorAll('#mobile-menu .nav-link').forEach(link => {
    link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.clientHeight;
        if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('text-oren');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('text-oren');
        }
    });
});

document.getElementById('current-year').textContent = new Date().getFullYear();

document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
        if (!this.hasAttribute('data-fallback')) {
            this.setAttribute('data-fallback', 'true');
            this.src = 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600';
        }
    });
});

setTimeout(() => {
    showNotification('📚 Selamat datang di Perpustakaan Digital SMAN 1 Tambang!', 'success');
}, 1000);

setTimeout(() => {
    showNotification('💬 Fitur Notifikasi WhatsApp aktif! Anda akan mendapat notifikasi keterlambatan peminjaman via WA', 'info');
}, 3000);
</script>

<style>
@media (max-width: 640px) {
    .navbar-scrolled { background: white !important; }
    .btn-primary, .btn-secondary { padding-left: 1.5rem; padding-right: 1.5rem; font-size: 0.875rem; }
    .feature-card { padding: 1.5rem; }
}

.bg-white\/10 { background: rgba(255, 255, 255, 0.1); }
.backdrop-blur-sm { backdrop-filter: blur(8px); }

* { -webkit-tap-highlight-color: transparent; }
img { transition: opacity 0.3s ease; }

.text-hitam { color: #1F2937; }
.text-oren { color: #F97316; }
.text-biru { color: #3B82F6; }
.text-hijau { color: #10B981; }
.bg-biru { background-color: #3B82F6; }
.bg-oren { background-color: #F97316; }
.bg-hijau { background-color: #10B981; }
.hover\:text-oren:hover { color: #F97316; }
.hover\:bg-oren:hover { background-color: #F97316; }
.hover\:bg-biru:hover { background-color: #3B82F6; }
.hover\:bg-hijau:hover { background-color: #10B981; }
</style>

</body>
</html>