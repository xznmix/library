@extends('kepala-pustaka.layouts.app')

@section('title', 'Laporan Denda')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Laporan Denda
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Analisis dan rekap penerimaan denda perpustakaan
            </p>
        </div>
        
        <div class="flex gap-2 mt-4 md:mt-0">
            <button onclick="exportToPDF()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export PDF
            </button>
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Info Periode --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg px-5 py-3 border border-blue-200 dark:border-blue-800 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-sm text-blue-800 dark:text-blue-300">
                Periode: <span class="font-semibold">{{ $startDate->format('d/m/Y') }}</span> s/d <span class="font-semibold">{{ $endDate->format('d/m/Y') }}</span>
            </span>
        </div>
        <span class="text-xs bg-blue-200 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
            {{ $totalTransaksi }} transaksi
        </span>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Periode</label>
                <select name="periode" id="periodeSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="hari_ini" {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="minggu_ini" {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="bulan_ini" {{ request('periode', 'bulan_ini') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="tahun_ini" {{ request('periode') == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="kustom" {{ request('periode') == 'kustom' ? 'selected' : '' }}>Kustom</option>
                </select>
            </div>
            
            <div id="startDateDiv" class="{{ request('periode') == 'kustom' ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div id="endDateDiv" class="{{ request('periode') == 'kustom' ? '' : 'hidden' }}">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="">Semua Status</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Petugas</label>
                <select name="petugas_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="">Semua Petugas</option>
                    @foreach($petugas ?? [] as $p)
                        <option value="{{ $p->id }}" {{ request('petugas_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Tampilkan
                </button>
                <a href="{{ route('kepala-pustaka.laporan.denda') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Statistik Denda --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Denda</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Transaksi</p>
                    <p class="text-3xl font-bold">{{ $totalTransaksi }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Rata-rata Denda</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($rataDenda, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Denda Tertinggi</p>
                    <p class="text-3xl font-bold">Rp {{ number_format($dendaTertinggi, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Tambahan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Komposisi Denda</p>
                    <div class="mt-2 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Denda Terlambat</span>
                            <span class="font-semibold text-indigo-600">Rp {{ number_format($totalDendaTerlambat, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Denda Rusak</span>
                            <span class="font-semibold text-orange-600">Rp {{ number_format($totalDendaRusak, 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            @php $persenTerlambat = $totalDenda > 0 ? ($totalDendaTerlambat / $totalDenda) * 100 : 0; @endphp
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $persenTerlambat }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="w-20 h-20">
                    <canvas id="chartKomposisiMini" width="80" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Status Verifikasi</p>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded-lg">
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">Pending</p>
                    <p class="text-xl font-bold text-yellow-700 dark:text-yellow-300">{{ $verifikasi['pending'] ?? 0 }}</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 p-2 rounded-lg">
                    <p class="text-xs text-green-600 dark:text-green-400">Disetujui</p>
                    <p class="text-xl font-bold text-green-700 dark:text-green-300">{{ $verifikasi['disetujui'] ?? 0 }}</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 p-2 rounded-lg">
                    <p class="text-xs text-red-600 dark:text-red-400">Ditolak</p>
                    <p class="text-xl font-bold text-red-700 dark:text-red-300">{{ $verifikasi['ditolak'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Denda --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart 1: Tren Denda Harian (LINE CHART) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    📈 Tren Denda Harian
                </h3>
                <div class="flex gap-2">
                    <span class="text-xs bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2 py-1 rounded-full">
                        Total: Rp {{ number_format(array_sum($grafikHarian['data'] ?? []), 0, ',', '.') }}
                    </span>
                </div>
            </div>
            
            @if(empty($grafikHarian['data']) || array_sum($grafikHarian['data']) == 0)
                <div class="h-80 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <svg class="w-20 h-20 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    <p class="text-gray-400 dark:text-gray-500 text-center">Belum ada data denda di periode ini</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                    </p>
                </div>
            @else
                <div class="relative" style="height: 320px;">
                    <canvas id="chartDendaHarian"></canvas>
                </div>
                <div class="text-center text-xs text-gray-400 dark:text-gray-500 mt-3">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 bg-indigo-600 rounded-full"></span> Total Denda per Hari (Rp)
                    </span>
                </div>
            @endif
        </div>

        {{-- Chart 2: Denda per Petugas (BAR CHART) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                👥 Denda per Petugas
            </h3>
            @if(empty($grafikPetugas['data']) || array_sum($grafikPetugas['data']) == 0)
                <div class="h-80 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <svg class="w-20 h-20 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <p class="text-gray-400 dark:text-gray-500">Belum ada data denda per petugas</p>
                </div>
            @else
                <div class="relative" style="height: 320px;">
                    <canvas id="chartPetugas"></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- 10 Denda Terbesar --}}
    @if(isset($dendaTerbesar) && count($dendaTerbesar) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
            <h3 class="font-semibold text-gray-800 dark:text-white">🏆 10 Denda Terbesar</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($dendaTerbesar as $index => $denda)
            <div class="p-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br 
                    @if($index == 0) from-yellow-400 to-yellow-600
                    @elseif($index == 1) from-gray-300 to-gray-500
                    @elseif($index == 2) from-orange-300 to-orange-500
                    @else from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 @endif
                    flex items-center justify-center text-white font-bold">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <div class="flex justify-between">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $denda->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $denda->buku->judul }}</p>
                        </div>
                        <span class="text-lg font-bold text-red-600">Rp {{ number_format($denda->denda_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex gap-3 mt-1 text-xs text-gray-500">
                        <span>Petugas: {{ $denda->petugas->name }}</span>
                        <span>•</span>
                        <span>{{ $denda->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel Denda --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white">📋 Detail Transaksi Denda</h3>
            <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                {{ $dendas->total() ?? 0 }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Denda Terlambat</th>
                        <th class="px-4 py-3 text-left">Denda Rusak</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Keterlambatan</th>
                        <th class="px-4 py-3 text-left">Kondisi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($dendas as $index => $denda)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">{{ $dendas->firstItem() + $index }}</td>
                        <td class="px-4 py-3">{{ $denda->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $denda->petugas->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $denda->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium line-clamp-1">{{ $denda->buku->judul ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $denda->buku->pengarang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($denda->denda, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($denda->denda_rusak, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-medium text-red-600">Rp {{ number_format($denda->denda_total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($denda->tgl_jatuh_tempo);
                                $kembali = \Carbon\Carbon::parse($denda->tanggal_pengembalian);
                                $terlambat = $kembali->diffInDays($jatuhTempo);
                            @endphp
                            <span class="px-2 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-full text-xs">
                                {{ $terlambat }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($denda->kondisi_kembali == 'baik')
                                <span class="text-green-600">Baik</span>
                            @elseif($denda->kondisi_kembali == 'rusak_ringan')
                                <span class="text-yellow-600">Rusak Ringan</span>
                            @elseif($denda->kondisi_kembali == 'rusak_berat')
                                <span class="text-orange-600">Rusak Berat</span>
                            @elseif($denda->kondisi_kembali == 'hilang')
                                <span class="text-red-600">Hilang</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($denda->status_verifikasi == 'disetujui')
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Disetujui</span>
                            @elseif($denda->status_verifikasi == 'ditolak')
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs">Ditolak</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Tidak ada data denda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($dendas instanceof \Illuminate\Pagination\LengthAwarePaginator && $dendas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $dendas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Chart container styling */
#chartDendaHarian,
#chartPetugas {
    max-height: 300px;
    width: 100%;
}

/* Responsive chart */
@media (max-width: 768px) {
    #chartDendaHarian,
    #chartPetugas {
        max-height: 250px;
    }
}

/* Tambahan styling untuk chart container */
.chart-container {
    position: relative;
    height: 320px;
    width: 100%;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ... semua javascript chart disini ...
</script>
@endpush

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Toggle date inputs based on periode selection
document.getElementById('periodeSelect').addEventListener('change', function() {
    const startDiv = document.getElementById('startDateDiv');
    const endDiv = document.getElementById('endDateDiv');
    
    if (this.value === 'kustom') {
        startDiv.classList.remove('hidden');
        endDiv.classList.remove('hidden');
    } else {
        startDiv.classList.add('hidden');
        endDiv.classList.add('hidden');
    }
});

// ============================================================
// CHART 1: TREN DENDA HARIAN (LINE CHART)
// ============================================================
@if(!empty($grafikHarian['data']) && array_sum($grafikHarian['data']) > 0)
const ctxHarian = document.getElementById('chartDendaHarian').getContext('2d');

// Data untuk chart
const labelsHarian = {!! json_encode($grafikHarian['labels']) !!};
const dataHarian = {!! json_encode($grafikHarian['data']) !!};

// Cari max value untuk menentukan step size
const maxValue = Math.max(...dataHarian);
const stepSize = maxValue > 1000000 ? 500000 : (maxValue > 100000 ? 50000 : 10000);

new Chart(ctxHarian, {
    type: 'line',
    data: {
        labels: labelsHarian,
        datasets: [{
            label: 'Total Denda',
            data: dataHarian,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.05)',
            borderWidth: 3,
            fill: true,
            tension: 0.3,
            pointBackgroundColor: function(context) {
                const value = context.raw;
                if (value === 0) return '#cbd5e1';
                if (value > 100000) return '#ef4444';
                if (value > 50000) return '#f97316';
                return '#4f46e5';
            },
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: function(context) {
                const value = context.raw;
                if (value > 0) return 5;
                return 3;
            },
            pointHoverRadius: 7,
            pointHoverBackgroundColor: '#4f46e5',
            pointHoverBorderColor: '#fff',
            pointHoverBorderWidth: 2,
            spanGaps: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#e5e7eb',
                borderColor: '#4f46e5',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        let value = context.raw;
                        let formatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                        return label + ': ' + formatted;
                    },
                    afterBody: function(context) {
                        const total = dataHarian.reduce((a, b) => a + b, 0);
                        const percentage = ((context[0].raw / total) * 100).toFixed(1);
                        return `Persentase: ${percentage}% dari total`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                        if (value >= 1000) {
                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                        }
                        return 'Rp ' + value;
                    },
                    stepSize: stepSize,
                    maxTicksLimit: 8
                },
                title: {
                    display: true,
                    text: 'Nominal Denda (Rp)',
                    color: '#6b7280',
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 45,
                    minRotation: 45,
                    autoSkip: true,
                    maxTicksLimit: 10
                },
                title: {
                    display: true,
                    text: 'Tanggal',
                    color: '#6b7280',
                    font: {
                        size: 11
                    }
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        },
        elements: {
            line: {
                borderJoin: 'round'
            }
        }
    }
});
@endif

// ============================================================
// CHART 2: DENDA PER PETUGAS (BAR CHART) - DIPERBAIKI
// ============================================================
@if(!empty($grafikPetugas['data']) && array_sum($grafikPetugas['data']) > 0)
const ctxPetugas = document.getElementById('chartPetugas').getContext('2d');

// Filter data yang nilainya 0 untuk tampilan lebih rapi
const petugasLabels = {!! json_encode($grafikPetugas['labels']) !!};
const petugasData = {!! json_encode($grafikPetugas['data']) !!};

// Filter hanya yang punya data > 0
const filteredLabels = [];
const filteredData = [];

for (let i = 0; i < petugasLabels.length; i++) {
    if (petugasData[i] > 0) {
        filteredLabels.push(petugasLabels[i]);
        filteredData.push(petugasData[i]);
    }
}

// Warna gradasi untuk bar chart
const barColors = [
    '#4f46e5', '#7c3aed', '#8b5cf6', '#a78bfa', 
    '#c084fc', '#e879f9', '#d946ef', '#f0abfc'
];

new Chart(ctxPetugas, {
    type: 'bar',
    data: {
        labels: filteredLabels,
        datasets: [{
            label: 'Total Denda',
            data: filteredData,
            backgroundColor: filteredLabels.map((_, i) => barColors[i % barColors.length]),
            borderRadius: 8,
            barPercentage: 0.65,
            categoryPercentage: 0.8,
            hoverBackgroundColor: '#4f46e5',
            hoverBorderRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#e5e7eb',
                borderColor: '#4f46e5',
                borderWidth: 1,
                callbacks: {
                    label: function(context) {
                        let value = context.raw;
                        let formatted = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(value);
                        return 'Total Denda: ' + formatted;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)',
                    drawBorder: false
                },
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        }
                        if (value >= 1000) {
                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                        }
                        return 'Rp ' + value;
                    }
                },
                title: {
                    display: true,
                    text: 'Nominal Denda (Rp)',
                    color: '#6b7280',
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    maxRotation: 25,
                    minRotation: 25,
                    autoSkip: true
                },
                title: {
                    display: true,
                    text: 'Nama Petugas',
                    color: '#6b7280',
                    font: {
                        size: 11
                    }
                }
            }
        },
        layout: {
            padding: {
                left: 10,
                right: 10,
                top: 10,
                bottom: 10
            }
        }
    }
});
@endif

// ============================================================
// CHART 3: KOMPOSISI DENDA (DOUGHNUT CHART)
// ============================================================
@if($totalDenda > 0)
const ctxKomposisi = document.getElementById('chartKomposisiMini').getContext('2d');
new Chart(ctxKomposisi, {
    type: 'doughnut',
    data: {
        labels: ['Denda Terlambat', 'Denda Rusak'],
        datasets: [{
            data: [{{ $totalDendaTerlambat }}, {{ $totalDendaRusak }}],
            backgroundColor: ['#4f46e5', '#f97316'],
            borderWidth: 0,
            cutout: '70%',
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { 
                display: false 
            },
            tooltip: { 
                enabled: true,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw;
                        const total = {{ $totalDenda }};
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: Rp ${new Intl.NumberFormat('id-ID').format(value)} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
@endif

function exportToPDF() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("kepala-pustaka.laporan.denda.export-pdf") }}?' + params.toString();
}

function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("kepala-pustaka.laporan.denda.export-excel") }}?' + params.toString();
}
</script>
@endpush
@endsection