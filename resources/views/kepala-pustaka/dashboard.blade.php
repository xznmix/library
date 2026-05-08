@extends('kepala-pustaka.layouts.app')

@section('title', 'Dashboard Kepala Pustaka')

@section('content')
<div class="space-y-6">

    {{-- Header with Notifications --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard Kepala Pustaka
            </h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ route('kepala-pustaka.audit.buku') }}" 
               class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Audit Buku</span>
            </a>
            
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" 
                        class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="font-semibold">{{ $dendaPending ?? 0 }}</span> Pending
                </button>
                
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">Denda Pending</h4>
                        <a href="{{ route('kepala-pustaka.verifikasi.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($dendaPendingList ?? [] as $denda)
                        <a href="{{ route('kepala-pustaka.verifikasi.detail', $denda->id) }}" 
                           class="block p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $denda->user->name }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $denda->buku->judul }}</p>
                                </div>
                                <span class="text-sm font-bold text-red-600">
                                    Rp {{ number_format($denda->denda_total, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500">{{ $denda->created_at->diffForHumans() }}</span>
                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full">{{ $denda->petugas->name ?? 'Petugas' }}</span>
                            </div>
                        </a>
                        @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada denda pending</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4 Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-indigo-500 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Total Buku</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBuku) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Tersedia: {{ number_format($statistik['buku_tersedia'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Total Anggota</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAnggota) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Aktif: {{ number_format($statistik['anggota_aktif'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Peminjaman Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($statistik['peminjaman_hari_ini'] ?? 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Dipinjam: {{ number_format($statistik['buku_dipinjam'] ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border-l-4 border-yellow-500 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-500">Denda Bulan Ini</p>
                    <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($totalDendaBulanIni, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Row: GRAFIK KUNJUNGAN (LINE CHART) Full Width --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                        📊 Tren Kunjungan Perpustakaan (7 Hari Terakhir)
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Grafik line menunjukkan fluktuasi kunjungan harian</p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if(empty($grafikKunjungan['data']) || array_sum($grafikKunjungan['data']) == 0)
                <div class="h-80 flex flex-col items-center justify-center bg-gray-50 rounded-lg">
                    <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                    <p class="text-gray-400 text-lg">Belum ada data kunjungan</p>
                    <p class="text-sm text-gray-400 mt-1">Kunjungan akan muncul setelah anggota melakukan scan QR Code</p>
                </div>
            @else
                <div class="relative" style="height: 320px;">
                    <canvas id="chartKunjungan"></canvas>
                </div>
                
                {{-- Statistik Ringkas Grafik --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-4 border-t border-gray-100">
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Total 7 Hari</p>
                        <p class="text-xl font-bold text-gray-800">{{ number_format($grafikKunjungan['total']) }}</p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Rata-rata / Hari</p>
                        <p class="text-xl font-bold text-indigo-600">{{ number_format($grafikKunjungan['rata_rata'], 1) }}</p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Hari Tertinggi</p>
                        <p class="text-xl font-bold text-green-600">
                            @php
                                $maxValue = max($grafikKunjungan['data']);
                                $maxIndex = array_search($maxValue, $grafikKunjungan['data']);
                                $maxDay = $grafikKunjungan['labels'][$maxIndex] ?? '-';
                            @endphp
                            {{ $maxDay }} ({{ number_format($maxValue) }})
                        </p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">Trend</p>
                        <p class="text-xl font-bold {{ $grafikKunjungan['trend_color'] }}">
                            {{ $grafikKunjungan['trend_text'] }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Row: Anomali Denda & Stock Alert --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Anomali Denda --}}
        @if(count($anomaliDenda) > 0)
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <h3 class="font-semibold text-red-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                ⚠️ Deteksi Anomali Denda
            </h3>
            <div class="space-y-2 max-h-80 overflow-y-auto pr-2">
                @foreach($anomaliDenda as $anomali)
                <div class="bg-white p-3 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-800">{{ $anomali['petugas'] }}</p>
                        <p class="text-sm text-gray-600">Rata-rata: Rp {{ number_format($anomali['rata_denda'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Global: Rp {{ number_format($anomali['rata_global'], 0, ',', '.') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold
                        {{ $anomali['level'] == 'danger' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $anomali['selisih'] }}
                    </span>
                </div>
                @endforeach
            </div>
            <div class="mt-3 text-xs text-red-700 bg-white p-2 rounded-lg">
                <span class="font-medium">⚠️ Perhatian:</span> Petugas dengan anomali perlu dievaluasi
            </div>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <h3 class="font-semibold text-green-800 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                ✅ Sistem Denda Normal
            </h3>
            <p class="text-sm text-green-700">Tidak ditemukan anomali denda pada petugas. Semua dalam batas wajar.</p>
        </div>
        @endif

        {{-- Stock Alert --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
            <h3 class="font-semibold text-yellow-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                📦 Peringatan Stok
            </h3>
            
            @if(count($stokMenipis) > 0 || ($bukuHabis ?? 0) > 0)
                @if(count($stokMenipis) > 0)
                <div class="mb-3">
                    <p class="text-sm font-medium text-yellow-700 mb-2">📖 Stok Menipis (≤ 3):</p>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                        @foreach($stokMenipis as $buku)
                        <div class="bg-white p-2 rounded-lg flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $buku->judul }}</p>
                                <p class="text-xs text-gray-500">Sisa: {{ $buku->stok_tersedia }}/{{ $buku->stok }}</p>
                            </div>
                            <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">⚠️ Menipis</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if($bukuHabis > 0)
                <div class="bg-white p-3 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Buku Stok Habis</p>
                            <p class="text-xs text-gray-500">{{ $bukuHabis }} judul dengan stok 0</p>
                        </div>
                        <span class="text-xs bg-red-200 text-red-800 px-2 py-1 rounded-full">❌ Habis</span>
                    </div>
                </div>
                @endif
            @else
                <div class="bg-white p-4 rounded-lg text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>Semua buku dalam stok aman</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak ada buku dengan stok menipis atau habis</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Row: Buku Populer --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            📚 5 Buku Paling Populer Tahun Ini
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @forelse($bukuPopuler as $index => $buku)
            <div class="text-center group">
                <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br 
                    @if($index == 0) from-yellow-400 to-yellow-600
                    @elseif($index == 1) from-gray-300 to-gray-500
                    @elseif($index == 2) from-orange-300 to-orange-500
                    @else from-indigo-100 to-indigo-200
                    @endif
                    flex items-center justify-center text-white font-bold text-xl mb-3 shadow-lg group-hover:scale-110 transition-transform">
                    {{ $index + 1 }}
                </div>
                <p class="text-sm font-medium text-gray-800 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                    {{ $buku->judul }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $buku->peminjaman_count }}x dipinjam
                </p>
            </div>
            @empty
            <div class="col-span-5 text-center py-8 text-gray-500">
                <p>Belum ada data buku populer</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Row: Statistik Cepat Lainnya --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Kunjungan Hari Ini</p>
                    <p class="text-3xl font-bold">{{ number_format($kunjunganHariIni) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Pengembalian Hari Ini</p>
                    <p class="text-3xl font-bold">{{ number_format($statistik['pengembalian_hari_ini'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Anggota Baru Bulan Ini</p>
                    <p class="text-3xl font-bold">{{ number_format($statistik['anggota_baru_bulan_ini'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
[x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grafik Kunjungan (Line Chart)
    const grafikKunjungan = @json($grafikKunjungan ?? ['labels' => [], 'data' => []]);
    
    if (grafikKunjungan.labels && grafikKunjungan.labels.length > 0 && grafikKunjungan.data.some(v => v > 0)) {
        const ctx = document.getElementById('chartKunjungan')?.getContext('2d');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: grafikKunjungan.labels,
                    datasets: [{
                        label: 'Jumlah Kunjungan',
                        data: grafikKunjungan.data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            callbacks: {
                                label: function(context) {
                                    return `Kunjungan: ${context.raw} orang`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { color: 'rgba(156, 163, 175, 0.1)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }
});
</script>
@endpush