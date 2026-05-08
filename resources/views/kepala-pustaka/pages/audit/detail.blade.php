@extends('kepala-pustaka.layouts.app')

@section('title', 'Detail Audit Buku - ' . ($buku->judul ?? 'Buku'))

@section('content')
<div class="space-y-6">

    {{-- Header dengan Back Button --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('kepala-pustaka.audit.buku') }}" 
               class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Detail Audit Buku
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Informasi lengkap kondisi dan riwayat buku
                </p>
            </div>
        </div>
        
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <button @click="openOpnameModal" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Stock Opname
            </button>
        </div>
    </div>

    {{-- Informasi Buku --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Informasi Buku
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Buku</label>
                        <p class="text-lg font-mono font-semibold text-gray-900 dark:text-white">{{ $buku->kode_buku ?? 'B-'.str_pad($buku->id,5,'0',STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul Buku</label>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $buku->judul }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengarang</label>
                        <p class="text-gray-700 dark:text-gray-300">{{ $buku->pengarang ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ISBN</label>
                        <p class="text-gray-700 dark:text-gray-300">{{ $buku->isbn ?? '-' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</label>
                        <p class="text-gray-700 dark:text-gray-300">{{ $buku->kategori->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penerbit</label>
                        <p class="text-gray-700 dark:text-gray-300">{{ $buku->penerbit ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tahun Terbit</label>
                        <p class="text-gray-700 dark:text-gray-300">{{ $buku->tahun_terbit ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</label>
                        <p class="text-gray-700 dark:text-gray-300">Rp {{ number_format($buku->harga ?? 50000, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Kondisi Buku --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Stok</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $buku->stok ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tersedia</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $buku->stok_tersedia ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Rusak</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $buku->stok_rusak ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Hilang</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $buku->stok_hilang ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Estimasi Kerugian --}}
    @php
        $kerugian = (($buku->stok_rusak ?? 0) + ($buku->stok_hilang ?? 0)) * ($buku->harga ?? 50000);
    @endphp
    @if($kerugian > 0)
    <div class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl p-5 border border-red-200 dark:border-red-800">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-red-200 dark:bg-red-800 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-700 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Estimasi Kerugian</p>
                    <p class="text-3xl font-bold text-red-700 dark:text-red-300">
                        Rp {{ number_format($kerugian, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                *Rusak: {{ $buku->stok_rusak ?? 0 }} | Hilang: {{ $buku->stok_hilang ?? 0 }}
            </div>
        </div>
    </div>
    @endif

    {{-- Statistik Peminjaman --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Dipinjam (Semua Waktu)</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statistik['total_dipinjam'] ?? 0 }} kali</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Dipinjam Saat Ini</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $statistik['dipinjam_saat_ini'] ?? 0 }} buku</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400">Rata-rata Peminjaman/Bulan</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $statistik['rata_rata_perbulan'] ?? 0 }} kali</p>
        </div>
    </div>

    {{-- Riwayat Stock Opname --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Riwayat Stock Opname
            </h3>
        </div>
        <div class="overflow-x-auto">
            @if(count($riwayatOpname ?? []) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-left">Stok Sistem</th>
                        <th class="px-4 py-3 text-left">Stok Fisik</th>
                        <th class="px-4 py-3 text-left">Selisih</th>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($riwayatOpname as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $log->user->name }}</td>
                        <td class="px-4 py-3">{{ $log->stok_sistem }}</td>
                        <td class="px-4 py-3">{{ $log->stok_fisik }}</td>
                        <td class="px-4 py-3">
                            @if($log->selisih > 0)
                                <span class="px-2 py-1 bg-{{ $log->stok_sistem > $log->stok_fisik ? 'red' : 'green' }}-100 dark:bg-{{ $log->stok_sistem > $log->stok_fisik ? 'red' : 'green' }}-900/30 text-{{ $log->stok_sistem > $log->stok_fisik ? 'red' : 'green' }}-700 dark:text-{{ $log->stok_sistem > $log->stok_fisik ? 'red' : 'green' }}-400 rounded-full text-xs font-medium">
                                    {{ $log->stok_sistem > $log->stok_fisik ? '-' : '+' }}{{ $log->selisih }}
                                </span>
                            @else
                                <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $log->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat stock opname</p>
                    <p class="text-sm text-gray-400 mt-1">Lakukan stock opname untuk mencatat perubahan stok</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- 10 Peminjaman Terakhir --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                10 Peminjaman Terakhir
            </h3>
        </div>
        <div class="overflow-x-auto">
            @if(count($buku->peminjaman ?? []) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-4 py-3 text-left">Tanggal Pinjam</th>
                        <th class="px-4 py-3 text-left">Peminjam</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tanggal Kembali</th>
                        <th class="px-4 py-3 text-left">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($buku->peminjaman->take(10) as $pinjam)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $pinjam->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($pinjam->status_pinjam == 'dipinjam')
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">Dipinjam</span>
                            @elseif($pinjam->status_pinjam == 'dikembalikan')
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">Dikembalikan</span>
                            @elseif($pinjam->status_pinjam == 'terlambat')
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs font-medium">Terlambat</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full text-xs font-medium">{{ $pinjam->status_pinjam }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($pinjam->tanggal_kembali)
                                {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">Rp {{ number_format($pinjam->denda ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-16 h-16 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada riwayat peminjaman</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Stock Opname --}}
<div x-data="stockOpnameModal()" x-show="open" 
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto">
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" 
             @click="open = false"></div>
        
        <div class="relative bg-white dark:bg-gray-800 rounded-xl max-w-md w-full p-6 shadow-2xl transform transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Opname</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $buku->judul }}</p>
                </div>
                <button @click="open = false" class="ml-auto text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form @submit.prevent="submitOpname">
                @csrf
                <input type="hidden" name="buku_id" value="{{ $buku->id }}">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stok Sistem
                        </label>
                        <input type="number" x-model="stokSistem" readonly 
                               class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Stok Fisik <span class="text-red-500">*</span>
                        </label>
                        <input type="number" x-model="stokFisik" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                               :class="{ 'border-red-500': stokFisik != stokSistem && stokFisik !== null }">
                        <p x-show="stokFisik != stokSistem && stokFisik !== null" 
                           class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                            ⚠️ Ada selisih! Stok fisik berbeda dengan sistem.
                        </p>
                    </div>
                    
                    <div x-show="stokFisik != stokSistem && stokFisik !== null">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Keterangan
                        </label>
                        <textarea name="keterangan" x-model="keterangan" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                  placeholder="Jelaskan penyebab selisih..."></textarea>
                    </div>
                    
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                        <p class="text-xs text-yellow-800 dark:text-yellow-400 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                <strong>Jika stok fisik kurang:</strong> Akan dicatat sebagai hilang<br>
                                <strong>Jika stok fisik lebih:</strong> Akan menambah stok tersedia
                            </span>
                        </p>
                    </div>
                    
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="open = false" 
                                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockOpnameModal() {
    return {
        open: false,
        loading: false,
        stokSistem: {{ $buku->stok_tersedia ?? 0 }},
        stokFisik: {{ $buku->stok_tersedia ?? 0 }},
        keterangan: '',
        
        openOpnameModal() {
            this.stokSistem = {{ $buku->stok_tersedia ?? 0 }};
            this.stokFisik = {{ $buku->stok_tersedia ?? 0 }};
            this.keterangan = '';
            this.open = true;
        },
        
        async submitOpname() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('buku_id', {{ $buku->id }});
                formData.append('stok_fisik', this.stokFisik);
                formData.append('keterangan', this.keterangan);
                
                const response = await fetch('{{ route("kepala-pustaka.audit.stock-opname-page") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    this.open = false;
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }
}

// Inisialisasi modal
document.addEventListener('alpine:init', () => {
    // Modal sudah diinisialisasi dengan x-data
    console.log('Stock opname modal ready');
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .btn-print, button, .fixed, .modal, [x-data] {
        display: none !important;
    }
    body {
        background: white;
    }
    .card, .bg-white {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
@endpush
@endsection