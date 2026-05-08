@extends('kepala-pustaka.layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Laporan Peminjaman
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Rekap dan analisis peminjaman buku perpustakaan
            </p>
        </div>
        
        <div class="flex gap-2 mt-4 md:mt-0">
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Export Excel
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bulan)
                        <option value="{{ $i+1 }}" {{ request('bulan') == $i+1 ? 'selected' : '' }}>
                            {{ $bulan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jenis Anggota</label>
                <select name="jenis" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Jenis</option>
                    <option value="siswa" {{ request('jenis') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ request('jenis') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="pegawai" {{ request('jenis') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>Umum</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    Tampilkan
                </button>
                <a href="{{ route('kepala-pustaka.laporan.peminjaman') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Info Periode --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl px-5 py-3 border border-blue-200 dark:border-blue-800">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm text-blue-800 dark:text-blue-300">
                    Periode: <span class="font-semibold">{{ request('tahun', now()->year) }}</span>
                    @if(request('bulan'))
                        - <span class="font-semibold">{{ \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') }}</span>
                    @endif
                    @if(request('status'))
                        - Status: <span class="font-semibold">{{ ucfirst(request('status')) }}</span>
                    @endif
                    @if(request('jenis'))
                        - Jenis: <span class="font-semibold">{{ ucfirst(request('jenis')) }}</span>
                    @endif
                </span>
            </div>
            <div class="flex gap-4 text-xs">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400">Tepat Waktu:</span>
                    <span class="font-medium text-green-600">{{ $tepatWaktu }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400">Terlambat:</span>
                    <span class="font-medium text-red-600">{{ $terlambat }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                    <span class="text-gray-600 dark:text-gray-400">Dipinjam:</span>
                    <span class="font-medium text-blue-600">{{ $sedangDipinjam }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Peminjaman</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalPeminjaman) }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                @if($totalPeminjaman > 0)
                    <span class="text-green-600">↑ {{ number_format(($totalPeminjaman / max($totalPeminjamanTahunLalu ?? 1, 1)) * 100, 1) }}%</span> dari tahun lalu
                @endif
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sedang Dipinjam</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($sedangDipinjam) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                {{ $totalPeminjaman > 0 ? round(($sedangDipinjam / $totalPeminjaman) * 100, 1) : 0 }}% dari total
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tepat Waktu</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($tepatWaktu) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                {{ $totalPeminjaman > 0 ? round(($tepatWaktu / $totalPeminjaman) * 100, 1) : 0 }}% dari total
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Terlambat</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($terlambat) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-500">
                {{ $totalPeminjaman > 0 ? round(($terlambat / $totalPeminjaman) * 100, 1) : 0 }}% dari total
            </div>
        </div>
    </div>

    {{-- Statistik Tambahan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Statistik per Kategori --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Statistik per Kategori
                </h3>
            </div>
            <div class="p-5">
                <div class="space-y-3">
                    @forelse($statistikKategori ?? [] as $kategori)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $kategori->nama }}</span>
                            <span class="text-sm font-medium text-indigo-600">{{ $kategori->peminjaman_count ?? 0 }} peminjaman</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            @php $persen = max($statistikKategori->max('peminjaman_count'), 1) > 0 ? (($kategori->peminjaman_count ?? 0) / max($statistikKategori->max('peminjaman_count'), 1)) * 100 : 0; @endphp
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">Belum ada data kategori</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Statistik per Jenis Anggota --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Statistik per Jenis Anggota
                </h3>
            </div>
            <div class="p-5">
                @php
                    $jenisData = $statistikJenis ?? [
                        'siswa' => 0,
                        'guru' => 0,
                        'pegawai' => 0,
                        'umum' => 0,
                    ];
                    $totalJenis = array_sum($jenisData);
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Siswa</span>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($jenisData['siswa']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Guru</span>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($jenisData['guru']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-purple-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Pegawai</span>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($jenisData['pegawai']) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-gray-500 rounded-full"></span>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Umum</span>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($jenisData['umum']) }}</span>
                    </div>
                    
                    @if($totalJenis > 0)
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex h-2 rounded-full overflow-hidden">
                            <div class="bg-blue-500" style="width: {{ ($jenisData['siswa'] / $totalJenis) * 100 }}%"></div>
                            <div class="bg-green-500" style="width: {{ ($jenisData['guru'] / $totalJenis) * 100 }}%"></div>
                            <div class="bg-purple-500" style="width: {{ ($jenisData['pegawai'] / $totalJenis) * 100 }}%"></div>
                            <div class="bg-gray-500" style="width: {{ ($jenisData['umum'] / $totalJenis) * 100 }}%"></div>
                        </div>
                        <p class="text-xs text-center text-gray-500 mt-2">Total {{ number_format($totalJenis) }} peminjaman</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tren Peminjaman Bulanan (Tanpa Chart - Pakai Tabel) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Tren Peminjaman {{ request('tahun', now()->year) }}
            </h3>
            <p class="text-xs text-gray-500 mt-1">Data peminjaman per bulan sepanjang tahun</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-center">Jumlah Peminjaman</th>
                        <th class="px-4 py-3 text-center">% dari Total</th>
                        <th class="px-4 py-3 text-center">Trend</th>
                        <th class="px-4 py-3 text-center">Visualisasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php
                        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        $maxPeminjaman = max($grafikBulanan['data'] ?? [0]);
                    @endphp
                    
                    @foreach($bulanLabels as $index => $bulan)
                        @php
                            $jumlah = $grafikBulanan['data'][$index] ?? 0;
                            $persen = $totalPeminjaman > 0 ? round(($jumlah / $totalPeminjaman) * 100, 1) : 0;
                            $prevJumlah = $grafikBulanan['data'][$index - 1] ?? null;
                            $trend = '';
                            $trendClass = '';
                            if ($prevJumlah !== null && $prevJumlah > 0) {
                                $diff = round((($jumlah - $prevJumlah) / $prevJumlah) * 100, 1);
                                if ($diff > 0) {
                                    $trend = "↑ {$diff}%";
                                    $trendClass = 'text-green-600';
                                } elseif ($diff < 0) {
                                    $trend = "↓ " . abs($diff) . "%";
                                    $trendClass = 'text-red-600';
                                } else {
                                    $trend = "→ 0%";
                                    $trendClass = 'text-gray-500';
                                }
                            } else {
                                $trend = "-";
                                $trendClass = 'text-gray-400';
                            }
                            
                            $barWidth = $maxPeminjaman > 0 ? ($jumlah / $maxPeminjaman) * 100 : 0;
                            $barColor = match(true) {
                                $jumlah >= 100 => 'bg-green-500',
                                $jumlah >= 50 => 'bg-blue-500',
                                $jumlah >= 20 => 'bg-yellow-500',
                                $jumlah > 0 => 'bg-orange-500',
                                default => 'bg-gray-300 dark:bg-gray-600'
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-medium">{{ $bulan }}</td>
                            <td class="px-4 py-3 text-center font-medium">{{ number_format($jumlah) }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs {{ $persen > 10 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $persen }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center {{ $trendClass }}">{{ $trend }}</td>
                            <td class="px-4 py-3">
                                @if($jumlah > 0)
                                    <div class="flex items-center gap-2">
                                        <div class="w-full max-w-[150px] bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $barWidth }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $barWidth }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Tidak ada data</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <tr>
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ number_format($totalPeminjaman) }}</td>
                        <td class="px-4 py-3 text-center">100%</td>
                        <td class="px-4 py-3 text-center">-</td>
                        <td class="px-4 py-3 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Buku Populer & Anggota Teraktif --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Buku Populer --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Buku Terpopuler
                </h3>
                <p class="text-xs text-gray-500 mt-1">Berdasarkan jumlah peminjaman periode ini</p>
            </div>
            <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
                @forelse($bukuPopuler ?? [] as $index => $buku)
                <div class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br 
                        @if($index == 0) from-yellow-400 to-yellow-600 shadow-md
                        @elseif($index == 1) from-gray-300 to-gray-500
                        @elseif($index == 2) from-orange-300 to-orange-500
                        @else from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 @endif
                        flex items-center justify-center text-white font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white line-clamp-1">{{ $buku->judul }}</p>
                        <div class="flex gap-3 text-xs text-gray-500 mt-1">
                            <span>{{ $buku->pengarang ?? '-' }}</span>
                            <span>•</span>
                            <span>Kode: {{ $buku->kode_buku ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold text-indigo-600">{{ $buku->peminjaman_count ?? 0 }}</span>
                        <span class="text-xs text-gray-500">x pinjam</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada data buku populer</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Anggota Teraktif --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Anggota Teraktif
                </h3>
                <p class="text-xs text-gray-500 mt-1">Berdasarkan frekuensi peminjaman periode ini</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-gray-700">
                @forelse($anggotaAktif ?? [] as $index => $anggota)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr($anggota->name, 0, 1)) }}
                            </div>
                            @if($index == 0)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-yellow-400 rounded-full flex items-center justify-center text-xs font-bold">🥇</div>
                            @elseif($index == 1)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center text-xs font-bold">🥈</div>
                            @elseif($index == 2)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-orange-300 rounded-full flex items-center justify-center text-xs font-bold">🥉</div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $anggota->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($anggota->jenis ?? 'Anggota') }} • {{ $anggota->kelas ?? '-' }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-bold text-indigo-600">{{ $anggota->peminjaman_count }}x pinjam</span>
                                <span class="text-xs text-gray-400">|</span>
                                <span class="text-xs text-gray-500">{{ $anggota->nisn_nik ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-2 text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada data anggota aktif</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel Detail Peminjaman --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Detail Peminjaman
            </h3>
            <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                {{ $peminjaman->total() ?? 0 }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-right">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($peminjaman as $index => $pinjam)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3">{{ $peminjaman->firstItem() + $index }}</td>
                        <td class="px-4 py-3">{{ $pinjam->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $pinjam->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $pinjam->user->nisn_nik ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $jenisClass = match($pinjam->user->jenis ?? 'umum') {
                                    'siswa' => 'bg-blue-100 text-blue-700',
                                    'guru' => 'bg-green-100 text-green-700',
                                    'pegawai' => 'bg-purple-100 text-purple-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs {{ $jenisClass }}">
                                {{ ucfirst($pinjam->user->jenis ?? 'Umum') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium line-clamp-1">{{ $pinjam->buku->judul ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $pinjam->buku->pengarang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $pinjam->petugas->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($pinjam->status_pinjam == 'dipinjam')
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    Dipinjam
                                </span>
                            @elseif($pinjam->status_pinjam == 'terlambat')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Terlambat
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Kembali
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($pinjam->tgl_jatuh_tempo)
                                {{ \Carbon\Carbon::parse($pinjam->tgl_jatuh_tempo)->format('d/m/Y') }}
                                @if($pinjam->status_pinjam == 'dipinjam' && now() > $pinjam->tgl_jatuh_tempo)
                                    <span class="text-xs text-red-500 block">Terlambat {{ now()->diffInDays($pinjam->tgl_jatuh_tempo) }} hari</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium {{ $pinjam->denda_total > 0 ? 'text-red-600' : 'text-gray-500' }}">
                            Rp {{ number_format($pinjam->denda_total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada data peminjaman</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($peminjaman->count() > 0)
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold border-t border-gray-200 dark:border-gray-700">
                    <tr>
                        <td colspan="8" class="px-4 py-3 text-right">Total Denda:</td>
                        <td class="px-4 py-3 text-right text-red-600">
                            Rp {{ number_format($peminjaman->sum('denda_total'), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Pagination --}}
        @if($peminjaman instanceof \Illuminate\Pagination\LengthAwarePaginator && $peminjaman->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $peminjaman->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("kepala-pustaka.laporan.peminjaman.export-excel") }}?' + params.toString();
}
</script>
@endpush