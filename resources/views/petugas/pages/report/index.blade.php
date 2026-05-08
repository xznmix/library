@extends('petugas.layouts.app')

@section('title', 'Laporan Perpustakaan')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Laporan & Statistik
        </h1>
        <p class="text-sm text-gray-500 mt-1">Rekap data dan analisis kegiatan perpustakaan</p>
    </div>

    {{-- Statistik Cepat --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-8">
        @php
            $totalPinjam = \App\Models\Peminjaman::count();
            $totalAnggota = \App\Models\User::whereIn('role', ['siswa', 'guru', 'pegawai', 'umum'])
                ->where('status_anggota', 'active')  // ✅ HANYA AKTIF
                ->count();
            $totalBuku = \App\Models\Buku::count();
            $totalKunjungan = \App\Models\Kunjungan::count();
            
            // ✅ PERBAIKAN: Total denda dari tabel DENDA (yang sudah dibayar)
            $totalDenda = \App\Models\Denda::where('payment_status', 'paid')->sum('jumlah_denda');
        @endphp

        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($totalPinjam, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Peminjaman</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ number_format($totalAnggota, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Anggota (Aktif)</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalBuku, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Buku</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
            <p class="text-2xl font-bold text-purple-600">{{ number_format($totalKunjungan, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Kunjungan</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Denda (Lunas)</p>
        </div>
    </div>

    {{-- Grid Menu Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        {{-- Laporan Peminjaman --}}
        <a href="{{ route('petugas.report.peminjaman') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
                <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded-full">Peminjaman</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Laporan Peminjaman</h3>
            <p class="text-sm text-gray-500 mb-4">Rekap peminjaman buku berdasarkan periode, status, dan anggota</p>
            <div class="flex items-center text-indigo-600 text-sm font-medium group-hover:gap-2 transition-all">
                <span>Lihat Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        {{-- Laporan Anggota --}}
        <a href="{{ route('petugas.report.anggota') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-green-50 text-green-600 px-2 py-1 rounded-full">Anggota</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Laporan Anggota</h3>
            <p class="text-sm text-gray-500 mb-4">Data anggota aktif, pending, dan riwayat keanggotaan</p>
            <div class="flex items-center text-green-600 text-sm font-medium group-hover:gap-2 transition-all">
                <span>Lihat Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        {{-- Laporan Buku --}}
        <a href="{{ route('petugas.report.buku') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full">Buku</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Laporan Buku</h3>
            <p class="text-sm text-gray-500 mb-4">Koleksi buku, buku populer, dan status ketersediaan</p>
            <div class="flex items-center text-blue-600 text-sm font-medium group-hover:gap-2 transition-all">
                <span>Lihat Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        {{-- Laporan Kunjungan --}}
        <a href="{{ route('petugas.report.kunjungan') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </div>
                <span class="text-xs bg-purple-50 text-purple-600 px-2 py-1 rounded-full">Kunjungan</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Laporan Kunjungan</h3>
            <p class="text-sm text-gray-500 mb-4">Statistik pengunjung perpustakaan harian, bulanan, tahunan</p>
            <div class="flex items-center text-purple-600 text-sm font-medium group-hover:gap-2 transition-all">
                <span>Lihat Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        {{-- Laporan Denda --}}
        <a href="{{ route('petugas.report.denda') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-yellow-50 text-yellow-600 px-2 py-1 rounded-full">Denda</span>
            </div>
            <h3 class="font-semibold text-gray-800 mb-1">Laporan Denda</h3>
            <p class="text-sm text-gray-500 mb-4">Rekap denda keterlambatan dan pemasukan dari denda</p>
            <div class="flex items-center text-yellow-600 text-sm font-medium group-hover:gap-2 transition-all">
                <span>Lihat Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>

        {{-- Export All --}}
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
            </div>
            <h3 class="font-semibold text-lg mb-1">Export Semua Data</h3>
            <p class="text-sm text-indigo-100 mb-4">Download laporan lengkap dalam format Excel atau PDF</p>
            <div class="flex gap-2">
                <a href="{{ route('petugas.report.export.all.excel') }}" class="flex-1 bg-white text-indigo-600 text-center px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-50 transition-colors">
                    Excel
                </a>
                <a href="{{ route('petugas.report.export.all.pdf') }}" class="flex-1 bg-white text-indigo-600 text-center px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-50 transition-colors">
                    PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection