@extends('pimpinan.layouts.app')

@section('title', 'Export Data - Pimpinan')

@section('content')
<div class="space-y-6">

    {{-- Header dengan Background Gradient --}}
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl shadow-lg">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative px-6 py-8 md:px-8 md:py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Export Data</h1>
                            <p class="text-indigo-100 text-sm mt-1">Download laporan dalam berbagai format</p>
                        </div>
                    </div>
                </div>
                
                {{-- Info Ringkas --}}
                <div class="flex items-center gap-4 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-xl">
                    <div class="text-center">
                        <p class="text-xs text-indigo-200">Tahun Ajaran</p>
                        <p class="text-sm font-bold text-white">{{ date('Y') }}/{{ date('Y')+1 }}</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-xs text-indigo-200">Semester</p>
                        <p class="text-sm font-bold text-white">{{ date('m') <= 6 ? 'Genap' : 'Ganjil' }}</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-xs text-indigo-200">Periode</p>
                        <p class="text-sm font-bold text-white">{{ now()->format('F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Decorative Elements --}}
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -translate-x-16 translate-y-16"></div>
        <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full translate-x-24 -translate-y-24"></div>
    </div>

    {{-- Alert Messages dengan Animasi --}}
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm animate-slide-in">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm animate-slide-in">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">  {{-- Ubah dari md:grid-cols-4 menjadi md:grid-cols-3 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Total Laporan</p>
                    <p class="text-2xl font-bold text-gray-800">4</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wider">Format Tersedia</p>
                    <p class="text-2xl font-bold text-gray-800">2</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 flex gap-2">
                <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full">PDF</span>
                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Excel</span>
            </div>
        </div>
    </div>

    {{-- Card Export Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Export Peminjaman --}}
        <div class="group bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
            <div class="relative h-2 bg-gradient-to-r from-blue-500 to-blue-600"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-blue-50 group-hover:bg-blue-100 transition-colors duration-300 flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Peminjaman</h3>
                        <p class="text-xs text-gray-500">Lengkap & Detail</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 mb-5 leading-relaxed">
                    Data peminjaman buku, statistik peminjaman, dan tren peminjaman per periode.
                </p>
                
                <div class="space-y-2">
                    <a href="{{ url('pimpinan/export/download/peminjaman/pdf') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ url('pimpinan/export/download/peminjaman/excel') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Excel
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Export Kunjungan --}}
        <div class="group bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
            <div class="relative h-2 bg-gradient-to-r from-green-500 to-green-600"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-green-50 group-hover:bg-green-100 transition-colors duration-300 flex items-center justify-center">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Kunjungan</h3>
                        <p class="text-xs text-gray-500">Harian & Bulanan</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 mb-5 leading-relaxed">
                    Data kunjungan, jam sibuk, demografi pengunjung, dan statistik kunjungan.
                </p>
                
                <div class="space-y-2">
                    <a href="{{ url('pimpinan/export/download/kunjungan/pdf') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ url('pimpinan/export/download/kunjungan/excel') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Excel
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Export Keuangan --}}
        <div class="group bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
            <div class="relative h-2 bg-gradient-to-r from-yellow-500 to-orange-500"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-yellow-50 group-hover:bg-yellow-100 transition-colors duration-300 flex items-center justify-center">
                        <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Keuangan</h3>
                        <p class="text-xs text-gray-500">Denda & Verifikasi</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 mb-5 leading-relaxed">
                    Data denda, rekapitulasi pembayaran, status verifikasi, dan laporan keuangan.
                </p>
                
                <div class="space-y-2">
                    <a href="{{ url('pimpinan/export/download/keuangan/pdf') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ url('pimpinan/export/download/keuangan/excel') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Excel
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Export Kinerja --}}
        <div class="group bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
            <div class="relative h-2 bg-gradient-to-r from-purple-500 to-pink-500"></div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-xl bg-purple-50 group-hover:bg-purple-100 transition-colors duration-300 flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Kinerja</h3>
                        <p class="text-xs text-gray-500">KPI & Evaluasi</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 mb-5 leading-relaxed">
                    Data KPI, kinerja petugas, target vs realisasi, dan rekomendasi strategis.
                </p>
                
                <div class="space-y-2">
                    <a href="{{ url('pimpinan/export/download/kinerja/pdf') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <a href="{{ url('pimpinan/export/download/kinerja/excel') }}" 
                       class="w-full flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md group">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Excel
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Tambahan dengan Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Format File Info --}}
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Tentang Format File
            </h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">PDF (Portable Document Format)</p>
                        <p class="text-xs text-gray-500">Cocok untuk dicetak, dibagikan, dan diarsipkan. Tampilan tetap rapi di semua perangkat.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Excel (Microsoft Excel)</p>
                        <p class="text-xs text-gray-500">Cocok untuk analisis data lebih lanjut, dapat diedit dan difilter sesuai kebutuhan.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Penting --}}
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-5 border border-yellow-100">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Catatan Penting
            </h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-yellow-600">•</span>
                    <span>Data yang diekspor adalah data real-time dari sistem perpustakaan</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-600">•</span>
                    <span>File PDF siap cetak dengan format kop surat resmi SMAN 1 Tambang</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-600">•</span>
                    <span>File Excel dapat diolah lebih lanjut menggunakan Microsoft Excel atau software sejenis</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-yellow-600">•</span>
                    <span>Ekspor data ini hanya untuk keperluan resmi dan tidak boleh disebarluaskan tanpa izin</span>
                </li>
            </ul>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .animate-slide-in {
        animation: slideIn 0.5s ease-out;
    }
    
    /* Hover effect untuk cards */
    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(4px);
    }
</style>
@endpush