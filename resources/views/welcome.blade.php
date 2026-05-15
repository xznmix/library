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
        
        :root {
            --oren: #F97316;
            --oren-dark: #EA580C;
            --biru: #3B82F6;
            --biru-dark: #2563EB;
            --hijau: #10B981;
            --hijau-dark: #059669;
            --hitam: #1F2937;
            --putih: #FFFFFF;
            --abu: #6B7280;
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
        
        /* Button styles */
        .btn-primary {
            background: var(--biru);
            color: var(--putih);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(249,115,22,0.3);
            background: var(--oren-dark);
        }
        
        /* Feature card */
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
        
        /* Hero overlay */
        .hero-overlay {
            background: radial-gradient(circle at 10% 20%, rgba(59,130,246,0.05), rgba(249,115,22,0.02));
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
        
        /* Project Card Style */
        .project-card {
            background: linear-gradient(135deg, rgba(59,130,246,0.05) 0%, rgba(249,115,22,0.05) 100%);
            border-radius: 16px;
            border-left: 4px solid var(--oren);
            transition: all 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-left-color: var(--biru);
        }
        
        @media (max-width: 768px) {
            .feature-card {
                padding: 1.5rem;
            }
            .feature-icon {
                width: 55px;
                height: 55px;
            }
            .feature-icon i {
                font-size: 24px !important;
            }
            .modal-content {
                width: 95%;
                margin: 20px auto;
            }
            .project-card {
                padding: 0.75rem 1rem !important;
            }
        }
        
        /* Color utilities */
        .bg-biru { background-color: #3B82F6; }
        .bg-biru-dark { background-color: #2563EB; }
        .bg-oren { background-color: #F97316; }
        .bg-oren-dark { background-color: #EA580C; }
        .bg-hijau { background-color: #10B981; }
        
        .text-biru { color: #3B82F6; }
        .text-oren { color: #F97316; }
        .text-hijau { color: #10B981; }
        .text-hitam { color: #1F2937; }
        .text-abu { color: #6B7280; }
        
        .hover\:bg-biru-dark:hover { background-color: #2563EB; }
        .hover\:bg-oren-dark:hover { background-color: #EA580C; }
        .hover\:text-biru:hover { color: #3B82F6; }
        .hover\:text-oren:hover { color: #F97316; }
        
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
        
        @media (max-width: 640px) {
            .notification {
                bottom: 20px;
                right: 20px;
                left: 20px;
            }
        }
    </style>
</head>
<body>

<!-- ==================== NAVBAR ==================== -->
<header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4">
        <div class="flex items-center justify-between">
            <a href="#beranda" class="flex items-center gap-2 md:gap-3 group cursor-pointer">
                <div class="w-8 h-8 md:w-12 md:h-12 rounded-xl overflow-hidden shadow-lg transition-all group-hover:scale-105">
                    <img src="{{ asset('storage/logo.jpg') }}" 
                        alt="Logo Perpustakaan" 
                        class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="font-bold text-sm md:text-lg text-biru">TAMBANG ILMU</span>
                    <span class="text-[10px] md:text-xs text-gray-500 block">Perpustakaan SMAN 1 Tambang</span>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-6 lg:gap-8">
                <a href="#beranda" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Beranda</a>
                <a href="#fitur" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Fitur</a>
                <a href="#koleksi" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Koleksi</a>
                <a href="#statistik" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Statistik</a>
                <a href="#kontak" class="nav-link text-hitam hover:text-oren font-medium transition-colors">Kontak</a>
            </nav>
            
            <div class="flex items-center gap-2 md:gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-block text-hitam hover:text-oren font-medium transition-colors px-3 md:px-4 py-2 text-sm md:text-base">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="bg-biru hover:bg-biru-dark text-white px-3 md:px-5 py-1.5 md:py-2 rounded-full text-xs md:text-sm font-medium transition-all transform hover:scale-105">
                    Daftar
                </a>
                <button id="menu-toggle" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bars text-hitam text-lg"></i>
                </button>
            </div>
        </div>
        
        <div id="mobile-menu" class="hidden md:hidden mt-3 pb-3 flex flex-col gap-2">
            <a href="#beranda" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Beranda</a>
            <a href="#fitur" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Fitur</a>
            <a href="#koleksi" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Koleksi</a>
            <a href="#statistik" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Statistik</a>
            <a href="#kontak" class="nav-link text-hitam hover:text-oren font-medium py-2 transition-colors">Kontak</a>
        </div>
    </div>
</header>

<!-- ==================== HERO SECTION ==================== -->
<section id="beranda" class="min-h-screen flex items-center relative overflow-hidden pt-16 md:pt-20">
    <div class="absolute inset-0 hero-overlay"></div>
    <div class="absolute top-20 right-0 w-48 h-48 md:w-72 md:h-72 bg-oren rounded-full filter blur-3xl opacity-10 animate-float"></div>
    <div class="absolute bottom-20 left-0 w-64 h-64 md:w-96 md:h-96 bg-biru rounded-full filter blur-3xl opacity-10 animate-float" style="animation-delay: 2s;"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 md:gap-12 items-center">
            <div data-aos="fade-up" data-aos-duration="1000">
                <div class="inline-flex items-center gap-2 bg-oren/10 rounded-full px-3 md:px-4 py-1 md:py-2 mb-4 md:mb-6">
                    <i class="fas fa-graduation-cap text-oren text-xs md:text-sm"></i>
                    <span class="text-xs md:text-sm font-medium text-biru">SMAN 1 TAMBANG</span>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-4 md:mb-6">
                    <span class="text-biru">Perpustakaan Digital</span>
                    <br>
                    <span class="text-hitam">SMAN 1 Tambang</span>
                </h1>
                <p class="text-hitam text-sm md:text-lg mb-6 md:mb-8 leading-relaxed">
                    Akses ribuan koleksi buku, modul pembelajaran, dan ebook dari mana saja. 
                    Bergabunglah dengan komunitas pembelajar di Perpustakaan Digital SMAN 1 Tambang.
                </p>
                
                <!-- PROJECT CARD - DIPERINDAH -->
                <div class="project-card p-3 md:p-4 mb-6 md:mb-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 bg-gradient-to-br from-biru to-oren rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-sm md:text-base"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xs md:text-sm font-semibold text-hitam mb-1">
                                🎓 PROYEK TUGAS AKHIR S1
                            </h4>
                            <p class="text-xs md:text-sm text-abu leading-relaxed">
                                <span class="font-medium text-biru">ILVI MAULIDYA NURULISA</span> (22076010)
                            </p>
                            <p class="text-xs text-abu mt-0.5">
                                Program Studi Pendidikan Teknik Informatika
                            </p>
                            <p class="text-xs text-abu">
                                Universitas Negeri Padang
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-oren/10 rounded-full text-[10px] md:text-xs text-oren">
                                    <i class="fas fa-certificate"></i> Tugas Akhir
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-biru/10 rounded-full text-[10px] md:text-xs text-biru">
                                    <i class="fas fa-calendar-alt"></i> 2026
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4">
                    <a href="#koleksi" class="btn-primary px-6 md:px-8 py-2.5 md:py-3 rounded-full font-medium text-sm md:text-base">
                        <i class="fas fa-book-open"></i> Jelajahi Koleksi
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary px-6 md:px-8 py-2.5 md:py-3 rounded-full font-medium text-sm md:text-base">
                        <i class="fas fa-user-plus"></i> Masuk ke Akun Saya
                    </a>
                </div>
            </div>
            
            <div class="relative mt-8 lg:mt-0" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhM8UZLutwnqTMLmJFxvSPovWExUaI8mDgv1aHDmKKI-XyES6atUeOCgj0ZgzlnHC-qNuEYtd3AyLbhnOrUW5O_kCiIjH6iU2BXxDMyi3CAQUaohqbe7rMQBAJw_RmF1IRzP_wiGKywg-FPO5VLyCQNfbRX5r8bviF6rxzEwr3hTGaYmlFC-QDdQ8iVOY73/s1200/WhatsApp%20Image%202024-08-27%20at%2015.26.24.jpg" 
                         alt="Perpustakaan SMAN 1 Tambang"
                         class="w-full h-auto object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FITUR UNGGULAN SECTION ==================== -->
<section id="fitur" class="py-12 md:py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 md:mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-xs md:text-sm uppercase tracking-wider">Layanan Unggulan</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-hitam mt-2 mb-3 md:mb-4">Fitur Modern Perpustakaan Digital</h2>
            <p class="text-hitam text-sm md:text-base max-w-2xl mx-auto px-4">Kami menghadirkan berbagai fitur canggih untuk pengalaman membaca yang lebih baik</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fab fa-whatsapp text-white text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-hitam mb-2 md:mb-3">Notifikasi WhatsApp</h3>
                <p class="text-hitam text-xs md:text-sm">Dapatkan notifikasi keterlambatan pengembalian via WhatsApp</p>
                <span class="inline-block mt-3 md:mt-4 text-hijau text-xs md:text-sm font-semibold">Tersedia 24/7</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt text-white text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-hitam mb-2 md:mb-3">DRM Protection</h3>
                <p class="text-hitam text-xs md:text-sm">Keamanan konten digital terjamin dengan sistem DRM canggih</p>
                <span class="inline-block mt-3 md:mt-4 text-oren text-xs md:text-sm font-semibold">Aman & Terpercaya</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-home text-white text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-hitam mb-2 md:mb-3">Booking dari Rumah</h3>
                <p class="text-hitam text-xs md:text-sm">Pesan buku favorit dari rumah dan ambil di perpustakaan</p>
                <span class="inline-block mt-3 md:mt-4 text-oren text-xs md:text-sm font-semibold">Tanpa Antri</span>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-search text-white text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-bold text-hitam mb-2 md:mb-3">OPAC Cerdas</h3>
                <p class="text-hitam text-xs md:text-sm">Cari koleksi buku dengan mudah dan cepat</p>
                <span class="inline-block mt-3 md:mt-4 text-oren text-xs md:text-sm font-semibold">Mudah Digunakan</span>
            </div>
        </div>
    </div>
</section>

<!-- ==================== KOLEKSI SECTION ==================== -->
<section id="koleksi" class="py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 md:mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-xs md:text-sm uppercase tracking-wider">Koleksi Kami</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-hitam mt-2 mb-3 md:mb-4">Jelajahi Koleksi Perpustakaan</h2>
            <p class="text-hitam text-sm md:text-base max-w-2xl mx-auto px-4">Beragam koleksi tersedia untuk mendukung proses belajar mengajar</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <a href="{{ route('opac.index') }}?jenis=buku_cetak" class="card-elegant p-4 md:p-6 block" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-biru rounded-2xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-book text-white text-lg md:text-2xl"></i>
                </div>
                <h3 class="text-base md:text-xl font-bold text-hitam mb-1 md:mb-2">Buku Cetak</h3>
                <p class="text-hitam text-xs md:text-sm mb-2 md:mb-3">500+ koleksi buku fisik</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold text-xs md:text-sm">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren text-xs md:text-sm"></i>
                </div>
            </a>
            
            <a href="{{ route('opac.index') }}?jenis=ebook" class="card-elegant p-4 md:p-6 block" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-oren rounded-2xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-tablet-alt text-white text-lg md:text-2xl"></i>
                </div>
                <h3 class="text-base md:text-xl font-bold text-hitam mb-1 md:mb-2">Ebook</h3>
                <p class="text-hitam text-xs md:text-sm mb-2 md:mb-3">100+ koleksi ebook</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold text-xs md:text-sm">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren text-xs md:text-sm"></i>
                </div>
            </a>
            
            <a href="{{ route('opac.index') }}?jenis=modul" class="card-elegant p-4 md:p-6 block" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-biru rounded-2xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-file-alt text-white text-lg md:text-2xl"></i>
                </div>
                <h3 class="text-base md:text-xl font-bold text-hitam mb-1 md:mb-2">Bank Soal</h3>
                <p class="text-hitam text-xs md:text-sm mb-2 md:mb-3">Gratis akses bank soal</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold text-xs md:text-sm">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren text-xs md:text-sm"></i>
                </div>
            </a>
            
            <a href="{{ route('opac.index') }}?jenis=jurnal" class="card-elegant p-4 md:p-6 block" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 md:w-16 md:h-16 bg-oren rounded-2xl flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-newspaper text-white text-lg md:text-2xl"></i>
                </div>
                <h3 class="text-base md:text-xl font-bold text-hitam mb-1 md:mb-2">Modul</h3>
                <p class="text-hitam text-xs md:text-sm mb-2 md:mb-3">Modul ajar pembelajaran</p>
                <div class="flex items-center justify-between">
                    <span class="text-oren font-semibold text-xs md:text-sm">Lihat Koleksi</span>
                    <i class="fas fa-arrow-right text-oren text-xs md:text-sm"></i>
                </div>
            </a>
        </div>
        
        <div class="text-center mt-8 md:mt-12" data-aos="fade-up">
            <a href="{{ route('opac.index') }}" class="inline-flex items-center gap-2 px-6 md:px-8 py-2.5 md:py-3 bg-white border-2 border-oren text-oren rounded-full font-medium hover:bg-oren hover:text-white transition-all text-sm md:text-base">
                <i class="fas fa-search"></i> Cari di OPAC
                <i class="fas fa-arrow-right text-xs md:text-sm"></i>
            </a>
        </div>
    </div>
</section>

<!-- ==================== STATISTIK SECTION ==================== -->
<section id="statistik" class="py-12 md:py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-biru"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-8 md:mb-12" data-aos="fade-up">
            <span class="text-oren font-semibold text-xs md:text-sm uppercase tracking-wider">Perpustakaan dalam Angka</span>
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mt-2">Fakta & Capaian Kami</h2>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
            <div class="text-center p-4 md:p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 md:w-20 md:h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-2 md:mb-4">
                    <i class="fas fa-book-open text-oren text-xl md:text-3xl"></i>
                </div>
                <div class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-1 md:mb-2">500+</div>
                <p class="text-white/80 text-xs md:text-sm font-medium">Koleksi Buku Cetak</p>
            </div>
            
            <div class="text-center p-4 md:p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 md:w-20 md:h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-2 md:mb-4">
                    <i class="fas fa-tablet-alt text-oren text-xl md:text-3xl"></i>
                </div>
                <div class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-1 md:mb-2">100+</div>
                <p class="text-white/80 text-xs md:text-sm font-medium">Koleksi Ebook</p>
            </div>
            
            <div class="text-center p-4 md:p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 md:w-20 md:h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-2 md:mb-4">
                    <i class="fas fa-users text-oren text-xl md:text-3xl"></i>
                </div>
                <div class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-1 md:mb-2">400+</div>
                <p class="text-white/80 text-xs md:text-sm font-medium">Anggota Aktif</p>
            </div>
            
            <div class="text-center p-4 md:p-6 bg-white/10 backdrop-blur-sm rounded-2xl" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 md:w-20 md:h-20 mx-auto bg-oren/20 rounded-full flex items-center justify-center mb-2 md:mb-4">
                    <i class="fas fa-calendar-check text-oren text-xl md:text-3xl"></i>
                </div>
                <div class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-1 md:mb-2">300+</div>
                <p class="text-white/80 text-xs md:text-sm font-medium">Transaksi per Bulan</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer id="kontak" class="bg-white border-t border-gray-100 pt-8 md:pt-16 pb-6 md:pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8 mb-8 md:mb-12">
            <div data-aos="fade-up">
                <div class="flex items-center gap-2 mb-3 md:mb-4">
                    <div class="flex items-center gap-2 mb-3 md:mb-4">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/logo.jpg') }}" 
                                alt="Logo Perpustakaan" 
                                class="w-full h-full object-cover">
                        </div>
                        <div>
                            <span class="font-bold text-sm md:text-base text-biru">TAMBANG ILMU</span>
                            <span class="text-[10px] md:text-xs text-gray-500 block">Perpustakaan Digital</span>
                        </div>
                    </div>
                </div>
                <p class="text-hitam text-xs md:text-sm leading-relaxed">
                    Perpustakaan digital SMAN 1 Tambang menyediakan akses mudah ke berbagai koleksi literasi.
                </p>
                <div class="flex gap-2 md:gap-3 mt-3 md:mt-4">
                    <a href="#" class="w-7 h-7 md:w-8 md:h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-green-500 hover:text-white transition-colors">
                        <i class="fab fa-whatsapp text-xs md:text-sm"></i>
                    </a>
                    <a href="#" class="w-7 h-7 md:w-8 md:h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-oren hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xs md:text-sm"></i>
                    </a>
                    <a href="#" class="w-7 h-7 md:w-8 md:h-8 bg-gray-100 rounded-full flex items-center justify-center text-hitam hover:bg-biru hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-xs md:text-sm"></i>
                    </a>
                </div>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="100">
                <h4 class="font-bold text-hitam text-sm md:text-base mb-3 md:mb-4">Tentang Kami</h4>
                <ul class="space-y-1 md:space-y-2 text-xs md:text-sm text-hitam">
                    <li><a href="#beranda" class="hover:text-oren transition-colors">Beranda</a></li>
                    <li><a href="#fitur" class="hover:text-oren transition-colors">Fitur</a></li>
                    <li><a href="#koleksi" class="hover:text-oren transition-colors">Koleksi</a></li>
                    <li><a href="#statistik" class="hover:text-oren transition-colors">Statistik</a></li>
                </ul>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="200">
                <h4 class="font-bold text-hitam text-sm md:text-base mb-3 md:mb-4">Layanan</h4>
                <ul class="space-y-1 md:space-y-2 text-xs md:text-sm text-hitam">
                    <li><a href="{{ route('opac.index') }}" class="hover:text-oren transition-colors">OPAC</a></li>
                    <li><a href="#" class="hover:text-oren transition-colors">E-Learning</a></li>
                </ul>
            </div>
            
            <div data-aos="fade-up" data-aos-delay="300">
                <h4 class="font-bold text-hitam text-sm md:text-base mb-3 md:mb-4">Hubungi Kami</h4>
                <ul class="space-y-2 md:space-y-3 text-xs md:text-sm text-hitam">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-oren text-sm mt-0.5"></i>
                        <span>Jl. Pekanbaru – Bangkinang KM.29, Tambang, Kampar</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-hijau text-sm"></i>
                        <span>+62 812-3456-7890</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope text-oren text-sm"></i>
                        <span>perpustakaan@sman1tambang.sch.id</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-clock text-oren text-sm"></i>
                        <span>Senin - Jumat: 07.00 - 16.00</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- FOOTER BAWAHAN - PROJECT INFO -->
        <div class="border-t border-gray-200 pt-6 md:pt-8">
            <div class="text-center mb-4">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-biru/5 to-oren/5 rounded-full mb-3">
                    <i class="fas fa-graduation-cap text-oren text-xs"></i>
                    <span class="text-[10px] md:text-xs text-abu font-medium">TUGAS AKHIR S1</span>
                </div>
                <p class="text-xs md:text-sm text-abu leading-relaxed max-w-2xl mx-auto">
                    <span class="font-semibold text-biru">ILVI MAULIDYA NURULISA</span> (22076010) — 
                    Program Studi Pendidikan Teknik Informatika, Universitas Negeri Padang
                </p>
            </div>
            <p class="text-hitam text-xs md:text-sm text-center">
                &copy; <span id="current-year"></span> Perpustakaan SMAN 1 Tambang | 
                <span class="text-oren">TAMBANG ILMU</span> - Mencerdaskan Generasi
            </p>
        </div>
    </div>
</footer>

<!-- ==================== NOTIFICATION CONTAINER ==================== -->
<div id="notification-container" class="fixed bottom-4 md:bottom-5 right-4 md:right-5 z-50 space-y-2"></div>

<!-- ==================== SCRIPTS ==================== -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ once: true, duration: 800 });

function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? '#10B981' : (type === 'warning' ? '#F97316' : '#3B82F6');
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle');
    
    notification.className = `notification p-3 md:p-4 rounded-xl shadow-lg flex items-center gap-2 md:gap-3 text-white`;
    notification.style.backgroundColor = bgColor;
    notification.innerHTML = `<i class="fas ${icon} text-sm md:text-base"></i><span class="text-xs md:text-sm">${message}</span>`;
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
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

setTimeout(() => {
    showNotification('📚 Selamat datang di Perpustakaan Digital SMAN 1 Tambang!', 'success');
}, 1000);
</script>

</body>
</html>