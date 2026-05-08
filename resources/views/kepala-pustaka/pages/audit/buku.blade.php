@extends('kepala-pustaka.layouts.app')

@section('title', 'Audit Buku')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Audit & Monitoring Buku</h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau kondisi stok, buku rusak, dan potensi kecurangan</p>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </button>
            <button 
                type="button"
                onclick="window.location.href='{{ route('kepala-pustaka.audit.stock-opname-page') }}'" 
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center gap-2 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Stock Opname
            </button>
        </div>
    </div>

    {{-- Filter Row --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList ?? [] as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Kondisi</label>
                <select name="kondisi" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Kondisi</option>
                    <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                    <option value="menipis" {{ request('kondisi') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="habis" {{ request('kondisi') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Pencarian</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari judul, pengarang, ISBN..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                        Filter
                    </button>
                    @if(request()->anyFilled(['kategori', 'kondisi', 'search']))
                        <a href="{{ route('kepala-pustaka.audit.buku') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Statistik Detail --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500">Total Judul</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalBuku ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500">Total Eksemplar</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalEksemplar ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500">Tersedia</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($totalTersedia ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500">Rusak</p>
            <p class="text-2xl font-bold text-orange-600">{{ number_format($bukuRusak ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
            <p class="text-xs text-gray-500">Hilang</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($bukuHilang ?? 0) }}</p>
        </div>
    </div>

    {{-- Total Kerugian --}}
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-5 border border-red-200">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-red-200 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Estimasi Kerugian</p>
                    <p class="text-2xl font-bold text-red-700">
                        Rp {{ number_format($totalKerugian ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                *Estimasi berdasarkan harga buku (Rp 50.000 jika tidak diisi)
            </div>
        </div>
    </div>

    {{-- Grafik Tren Kerusakan --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800">📊 Tren Kerusakan & Kehilangan (6 Bulan)</h3>
                <div class="flex gap-3">
                    <span class="flex items-center gap-1 text-sm">
                        <span class="w-3 h-3 bg-orange-500 rounded-full"></span> Rusak
                    </span>
                    <span class="flex items-center gap-1 text-sm">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span> Hilang
                    </span>
                </div>
            </div>
            
            <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartKerusakan"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                ⚠️ Peringatan Stok
            </h3>
            
            <div class="space-y-4 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                @if(count($bukuMenipis ?? []) > 0)
                <div>
                    <p class="text-xs font-medium text-yellow-700 mb-2">Buku Stok Menipis (≤ 3):</p>
                    @foreach($bukuMenipis as $buku)
                    <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg mb-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $buku->judul }}</p>
                            <p class="text-xs text-gray-500">Stok: {{ $buku->stok_tersedia }}/{{ $buku->stok }}</p>
                        </div>
                        <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full whitespace-nowrap">
                            ⚠️ {{ $buku->stok_tersedia }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if(($bukuHabis ?? 0) > 0)
                <div>
                    <p class="text-xs font-medium text-red-700 mb-2">Buku Stok Habis:</p>
                    <div class="bg-red-50 p-3 rounded-lg">
                        <p class="text-sm text-red-700">{{ $bukuHabis }} judul buku dengan stok 0</p>
                    </div>
                </div>
                @endif

                @if(count($bukuKerugianTerbesar ?? []) > 0)
                <div>
                    <p class="text-xs font-medium text-indigo-700 mb-2">Kerugian Terbesar:</p>
                    @foreach($bukuKerugianTerbesar as $buku)
                    <div class="flex items-center justify-between p-2 bg-indigo-50 rounded-lg mb-2">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800 line-clamp-1">{{ $buku->judul }}</p>
                            <p class="text-xs text-gray-500">Rusak: {{ $buku->stok_rusak }} | Hilang: {{ $buku->stok_hilang }}</p>
                        </div>
                        <span class="text-xs font-bold text-red-600 whitespace-nowrap">
                            Rp {{ number_format($buku->kerugian, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SECTION MANAJEMEN AUDIT (SEDERHANA - OTOMATIS) --}}
    {{-- ============================================================ --}}
    <div class="mt-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-1 h-6 bg-indigo-600 rounded-full"></div>
            <h2 class="text-lg font-semibold text-gray-800">📋 Manajemen Audit</h2>
            <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Otomatis dari buku bermasalah</span>
        </div>

        {{-- Statistik Mini --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-yellow-600">⏳ Antrian Audit</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $statistikAudit['total_antrian'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600">✓ Selesai Diaudit</p>
                        <p class="text-2xl font-bold text-green-700">{{ $statistikAudit['total_selesai'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-red-600">⚠️ Total Bermasalah</p>
                        <p class="text-2xl font-bold text-red-700">{{ $statistikAudit['total_bermasalah'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Antrian Audit --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="font-medium text-gray-800">📋 Antrian Audit Buku</h3>
                <p class="text-xs text-gray-500 mt-1">Buku bermasalah otomatis masuk antrian. Lakukan stock opname untuk menyelesaikan audit.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            {{-- <th class="px-4 py-3 text-left">Kode Buku</th> --}}
                            <th class="px-4 py-3 text-left">Judul Buku</th>
                            <th class="px-4 py-3 text-left">Masalah</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($antrianAudit ?? [] as $index => $queue)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $loop->iteration }}</td>
                            {{-- <td class="px-4 py-3 font-mono text-xs">{{ $queue->buku->kode_buku ?? '-' }}</td> --}}
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $queue->buku->judul ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $masalah = [];
                                    if(($queue->buku->stok_rusak ?? 0) > 0) $masalah[] = 'Rusak (' . $queue->buku->stok_rusak . ')';
                                    if(($queue->buku->stok_hilang ?? 0) > 0) $masalah[] = 'Hilang (' . $queue->buku->stok_hilang . ')';
                                    if(($queue->buku->stok_tersedia ?? 0) <= 3 && ($queue->buku->stok_tersedia ?? 0) > 0) $masalah[] = 'Stok Menipis (' . $queue->buku->stok_tersedia . ')';
                                    if(($queue->buku->stok_tersedia ?? 0) == 0) $masalah[] = 'Stok Habis';
                                @endphp
                                @foreach($masalah as $m)
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs mr-1 mb-1">{{ $m }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-3">
                                @if($queue->status == 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Menunggu Audit</span>
                                @elseif($queue->status == 'in_progress')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Sedang Diaudit</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($queue->status == 'pending')
                                    <button onclick="updateQueueStatus({{ $queue->id }}, 'in_progress')" 
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition">
                                        Mulai Audit
                                    </button>
                                @elseif($queue->status == 'in_progress')
                                    <button onclick="openOpnameModal({{ $queue->buku_id }}, '{{ addslashes($queue->buku->judul) }}', {{ $queue->buku->stok_tersedia }})" 
                                            class="px-3 py-1 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700 transition">
                                        Stock Opname
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Tidak ada antrian audit</p>
                                    <p class="text-xs mt-1">Semua buku dalam kondisi baik</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Riwayat Audit Selesai --}}
        @if(count($sudahDiaudit ?? []) > 0)
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="font-medium text-gray-800">✅ Riwayat Audit Selesai</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Buku</th>
                            <th class="px-4 py-2 text-left">Tanggal Selesai</th>
                            <th class="px-4 py-2 text-left">Auditor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sudahDiaudit as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $log->buku->judul ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $log->buku->kode_buku ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->completed_date)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $log->assignedBy->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Tabel Daftar Buku Bermasalah --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800">Daftar Buku Bermasalah</h3>
            </div>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                {{ $bukuAudit->total() ?? 0 }} buku
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left rounded-tl-xl">No</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Judul Buku</th>
                        <th class="px-4 py-3 text-left">Pengarang</th>
                        <th class="px-4 py-3 text-left">Stok</th>
                        <th class="px-4 py-3 text-left">Rusak</th>
                        <th class="px-4 py-3 text-left">Hilang</th>
                        <th class="px-4 py-3 text-left">Kerugian</th>
                        <th class="px-4 py-3 text-center rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bukuAudit ?? [] as $index => $buku)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">{{ $bukuAudit->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-mono">{{ $buku->kode_buku ?? 'B-'.str_pad($buku->id,5,'0',STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $buku->judul }}</div>
                            <div class="text-xs text-gray-500">{{ $buku->isbn ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $buku->pengarang ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium {{ $buku->stok_tersedia <= 3 ? 'text-orange-600' : '' }}">
                                {{ $buku->stok_tersedia }}/{{ $buku->stok }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($buku->stok_rusak > 0)
                                <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-medium">
                                    {{ $buku->stok_rusak }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($buku->stok_hilang > 0)
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    {{ $buku->stok_hilang }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono font-medium text-red-600">
                            Rp {{ number_format(($buku->stok_rusak + $buku->stok_hilang) * ($buku->harga ?? 50000), 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openOpnameModal({{ $buku->id }}, '{{ addslashes($buku->judul) }}', {{ $buku->stok_tersedia }})" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors group"
                                        title="Stock Opname">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('kepala-pustaka.audit.buku.detail', $buku->id) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors group"
                                   title="Detail">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Tidak ada data buku bermasalah</p>
                                <p class="text-sm text-gray-400 mt-1">Semua buku dalam kondisi baik</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($bukuAudit) && $bukuAudit->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $bukuAudit->withQueryString()->links() }}
        </div>
        @endif
    </div>

    {{-- Rekomendasi Audit --}}
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 border border-indigo-100">
        <h3 class="font-semibold text-indigo-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Rekomendasi Audit
        </h3>
        <ul class="space-y-2 text-sm text-indigo-800">
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Lakukan stock opname fisik untuk <strong>{{ $bukuRusak ?? 0 }}</strong> buku yang dilaporkan rusak</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Verifikasi ulang <strong>{{ $bukuHilang ?? 0 }}</strong> buku yang dilaporkan hilang</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Periksa <strong>{{ isset($bukuMenipis) ? $bukuMenipis->count() : 0 }}</strong> buku dengan stok menipis untuk pengadaan ulang</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-indigo-500">•</span>
                <span>Estimasi kerugian mencapai <strong>Rp {{ number_format($totalKerugian ?? 0, 0, ',', '.') }}</strong></span>
            </li>
        </ul>
    </div>
</div>

{{-- MODAL STOCK OPNAME --}}
<div id="opnameModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="display: none;">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeOpnameModal()"></div>
    <div class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-2xl">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Stock Opname</h3>
                        <p class="text-sm text-blue-200" id="modalBukuJudul"></p>
                    </div>
                </div>
                <button onclick="closeOpnameModal()" class="text-white/70 hover:text-white">
                    ✕
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <form id="opnameForm">
                @csrf
                <input type="hidden" name="buku_id" id="modalBukuId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Sistem</label>
                        <input type="number" id="stokSistemInput" readonly 
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-900">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Stok Fisik <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stokFisikInput" name="stok_fisik" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p id="selisihWarning" class="text-xs text-orange-600 mt-1 hidden">
                            ⚠️ Ada selisih! Stok fisik berbeda dengan sistem.
                        </p>
                    </div>
                    
                    <div id="keteranganDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                        <textarea name="keterangan" id="keteranganInput" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Jelaskan penyebab selisih..."></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                        <p class="text-xs text-yellow-800 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                <strong>Jika stok fisik kurang:</strong> Akan dicatat sebagai hilang<br>
                                <strong>Jika stok fisik lebih:</strong> Akan menambah stok tersedia
                            </span>
                        </p>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeOpnameModal()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" id="submitOpnameBtn"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ============================================================
// GRAFIK KERUSAKAN
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initChart, 100);
});

function initChart() {
    const canvas = document.getElementById('chartKerusakan');
    if (!canvas) return;
    
    let existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();
    
    canvas.style.height = '300px';
    canvas.style.width = '100%';
    
    const labels = {!! json_encode($grafikKerusakan['labels'] ?? ['Jan','Feb','Mar','Apr','Mei','Jun']) !!};
    const dataRusak = {!! json_encode($grafikKerusakan['rusak'] ?? [0,0,0,0,0,0]) !!};
    const dataHilang = {!! json_encode($grafikKerusakan['hilang'] ?? [0,0,0,0,0,0]) !!};
    
    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { label: 'Rusak', data: dataRusak, backgroundColor: '#f97316', borderRadius: 5 },
                { label: 'Hilang', data: dataHilang, backgroundColor: '#ef4444', borderRadius: 5 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } },
                tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + ctx.raw + ' eksemplar' } }
            },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
        }
    });
}

// ============================================================
// STOCK OPNAME MODAL
// ============================================================
let currentBukuId = null;
let currentStokSistem = 0;

function openOpnameModal(id, judul, stok) {
    currentBukuId = id;
    currentStokSistem = stok;
    
    document.getElementById('modalBukuId').value = id;
    document.getElementById('modalBukuJudul').innerText = judul;
    document.getElementById('stokSistemInput').value = stok;
    document.getElementById('stokFisikInput').value = '';
    document.getElementById('keteranganInput').value = '';
    
    document.getElementById('keteranganDiv').classList.add('hidden');
    document.getElementById('selisihWarning').classList.add('hidden');
    
    const modal = document.getElementById('opnameModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    document.body.classList.add('overflow-hidden');
}

function closeOpnameModal() {
    const modal = document.getElementById('opnameModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.body.classList.remove('overflow-hidden');
}

// Cek selisih stok realtime
document.getElementById('stokFisikInput')?.addEventListener('input', function() {
    const stokFisik = parseInt(this.value) || 0;
    const warning = document.getElementById('selisihWarning');
    const keteranganDiv = document.getElementById('keteranganDiv');
    
    if (this.value !== '' && stokFisik !== currentStokSistem) {
        warning.classList.remove('hidden');
        keteranganDiv.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
        keteranganDiv.classList.add('hidden');
    }
});

// Submit form opname
document.getElementById('opnameForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const stokFisik = document.getElementById('stokFisikInput').value;
    const keterangan = document.getElementById('keteranganInput').value;
    
    if (!stokFisik || stokFisik === '') {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Stok fisik harus diisi!',
            confirmButtonColor: '#4f46e5'
        });
        return;
    }
    
    const submitBtn = document.getElementById('submitOpnameBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('buku_id', currentBukuId);
        formData.append('stok_fisik', stokFisik);
        formData.append('keterangan', keterangan);
        
        const response = await fetch('{{ route("kepala-pustaka.audit.stock-opname.proses") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonColor: '#4f46e5',
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message,
                confirmButtonColor: '#4f46e5'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message,
            confirmButtonColor: '#4f46e5'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// ESC close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOpnameModal();
    }
});

// ============================================================
// UPDATE QUEUE STATUS
// ============================================================
async function updateQueueStatus(id, status) {
    const result = await Swal.fire({
        title: 'Konfirmasi',
        text: status === 'in_progress' ? 'Mulai audit buku ini?' : 'Selesaikan audit ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch('{{ url("kepala-pustaka/audit/queue") }}/' + id + '/status', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message
        });
    }
}

// ============================================================
// FUNGSI LAINNYA
// ============================================================
function exportToExcel() {
    window.location.href = '{{ route("kepala-pustaka.audit.export") }}' + window.location.search;
}
</script>
@endpush

@push('styles')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.chart-container { position: relative; height: 300px; width: 100%; max-height: 300px; overflow: hidden; }
.chart-container canvas { display: block; width: 100% !important; height: 100% !important; max-height: 300px !important; }
</style>
@endpush
@endsection