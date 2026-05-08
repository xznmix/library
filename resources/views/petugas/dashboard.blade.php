@extends('petugas.layouts.app')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="space-y-6">

    {{-- Header dengan Tanggal --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Selamat Datang, {{ auth()->user()->name ?? 'Petugas' }}
            </h2>
            <p class="text-gray-500 flex items-center gap-2 mt-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        
        <div class="bg-white px-6 py-3 rounded-xl shadow-sm border border-gray-200">
            <div class="text-sm text-gray-500">Waktu Sistem</div>
            <div class="text-xl font-bold text-indigo-700" id="realtime-clock">--:--:--</div>
        </div>
    </div>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 text-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-indigo-200 text-sm font-medium">TOTAL KOLEKSI</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($totalBuku) }}</div>
                    <div class="text-indigo-200 text-xs mt-2">
                        <span class="bg-indigo-500 bg-opacity-50 px-2 py-1 rounded">{{ number_format($totalDigital) }} Digital</span>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-600 to-blue-800 text-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-blue-200 text-sm font-medium">ANGGOTA</div>
                    <div class="text-3xl font-bold mt-2">{{ number_format($totalAnggota) }}</div>
                    <div class="text-blue-200 text-xs mt-2">
                        <span class="bg-blue-500 bg-opacity-50 px-2 py-1 rounded">{{ $peminjamanAktif }} Peminjaman Aktif</span>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-600 to-green-800 text-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-green-200 text-sm font-medium">KUNJUNGAN HARI INI</div>
                    <div class="text-3xl font-bold mt-2">{{ $kunjunganHariIni }}</div>
                    <div class="text-green-200 text-xs mt-2">
                        <span class="bg-green-500 bg-opacity-50 px-2 py-1 rounded">Total Kunjungan</span>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-sm bg-green-500 bg-opacity-30 p-2 rounded-lg">
                <div class="flex justify-between">
                    <span>Pengembalian Hari Ini:</span>
                    <span class="font-bold">{{ $pengembalianHariIni }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Jatuh Tempo:</span>
                    <span class="font-bold text-yellow-200">{{ $jatuhTempoHariIni }}</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <div class="text-amber-100 text-sm font-medium">DENDA BULAN INI</div>
                    <div class="text-3xl font-bold mt-2">Rp {{ number_format($totalDendaBulanIni, 0, ',', '.') }}</div>
                    <div class="text-amber-100 text-xs mt-2">
                        <span class="bg-amber-600 bg-opacity-50 px-2 py-1 rounded">{{ $totalTerlambat }} Keterlambatan</span>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK PEMINJAMAN & KUNJUNGAN 7 HARI --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Grafik Aktivitas 7 Hari Terakhir
            </h3>
            <div class="flex gap-3">
                <span class="flex items-center gap-1 text-sm">
                    <span class="w-3 h-3 bg-indigo-500 rounded-full"></span> Peminjaman
                </span>
                <span class="flex items-center gap-1 text-sm">
                    <span class="w-3 h-3 bg-green-500 rounded-full"></span> Kunjungan
                </span>
            </div>
        </div>
        
        @if(empty($grafikPeminjaman['data']) && empty($grafikKunjungan['data']))
            <div class="h-80 flex flex-col items-center justify-center bg-gray-50 rounded-lg">
                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p class="text-gray-400">Belum ada data peminjaman dan kunjungan</p>
            </div>
        @else
            <div class="relative" style="height: 350px;">
                <canvas id="chartAktivitas"></canvas>
            </div>
        @endif
        
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100">
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Total Peminjaman</p>
                <p class="text-xl font-bold text-indigo-600">{{ array_sum($grafikPeminjaman['data'] ?? [0]) }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Total Kunjungan</p>
                <p class="text-xl font-bold text-green-600">{{ array_sum($grafikKunjungan['data'] ?? [0]) }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Rata-rata Peminjaman/Hari</p>
                <p class="text-xl font-bold text-indigo-600">{{ round(array_sum($grafikPeminjaman['data'] ?? [0]) / 7, 1) }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500">Rata-rata Kunjungan/Hari</p>
                <p class="text-xl font-bold text-green-600">{{ round(array_sum($grafikKunjungan['data'] ?? [0]) / 7, 1) }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Aksi Cepat --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="font-semibold text-gray-800 text-lg mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Aksi Cepat
            </h3>
            
            <div class="grid grid-cols-2 gap-3 mb-6">
                <a href="{{ route('petugas.sirkulasi.peminjaman.create') }}" 
                   class="bg-blue-50 hover:bg-blue-100 p-4 rounded-xl text-center transition group border border-blue-200">
                    <div class="bg-blue-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Peminjaman</span>
                </a>
                
                <a href="{{ route('petugas.sirkulasi.pengembalian.index') }}" 
                   class="bg-green-50 hover:bg-green-100 p-4 rounded-xl text-center transition group border border-green-200">
                    <div class="bg-green-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Pengembalian</span>
                </a>
                
                <a href="{{ route('petugas.kunjungan.index') }}" 
                   class="bg-purple-50 hover:bg-purple-100 p-4 rounded-xl text-center transition group border border-purple-200">
                    <div class="bg-purple-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Kunjungan</span>
                </a>
                
                <a href="{{ route('petugas.keanggotaan.index') }}" 
                   class="bg-orange-50 hover:bg-orange-100 p-4 rounded-xl text-center transition group border border-orange-200">
                    <div class="bg-orange-500 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Anggota</span>
                </a>
            </div>
            
            {{-- Peringatan Jatuh Tempo --}}
            <div class="border-t pt-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Jatuh Tempo Hari Ini
                </h4>
                
                @if($jatuhTempoHariIni > 0)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-3xl font-bold text-red-600">{{ $jatuhTempoHariIni }}</p>
                                <p class="text-sm text-red-700">buku harus dikembalikan</p>
                            </div>
                            <a href="{{ route('petugas.sirkulasi.pengembalian.index') }}" 
                               class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition">
                                Proses →
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-700 text-center">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tidak ada buku jatuh tempo hari ini
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Buku Terpopuler (Sudah Konek Database) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="font-semibold text-gray-800 text-lg mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                Buku Paling Populer
            </h3>
            
            @if($bukuTerpopuler->count() > 0)
                <div class="space-y-3">
                    @foreach($bukuTerpopuler as $index => $buku)
                        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                @if($index == 0) bg-yellow-400 text-white
                                @elseif($index == 1) bg-gray-400 text-white
                                @elseif($index == 2) bg-orange-400 text-white                                @else bg-indigo-100 text-indigo-700 @endif">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">{{ $buku->judul ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $buku->pengarang ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-indigo-700">{{ $buku->peminjaman_count ?? 0 }}x</div>
                                <div class="text-xs text-gray-500">dipinjam</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada data peminjaman</p>
            @endif
        </div>

        {{-- Pembaca Teraktif (Sudah Konek Database) --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="font-semibold text-gray-800 text-lg mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Peminjaman Teraktif Bulan Ini
            </h3>
            
            @if($pembacaTeraktif->count() > 0)
                <div class="space-y-3">
                    @foreach($pembacaTeraktif as $index => $anggota)
                        <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                @if($index == 0) bg-yellow-400 text-white
                                @elseif($index == 1) bg-gray-400 text-white
                                @elseif($index == 2) bg-orange-400 text-white
                                @else bg-indigo-100 text-indigo-700 @endif">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">{{ $anggota->name ?? '-' }}</div>
                                <div class="text-sm text-gray-500">{{ ucfirst($anggota->jenis ?? 'Anggota') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-indigo-700">{{ $anggota->peminjaman_count ?? 0 }}x</div>
                                <div class="text-xs text-gray-500">dipinjam</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada aktivitas peminjaman</p>
            @endif
        </div>
    </div>

    {{-- Aktivitas Terkini --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Aktivitas Terkini
            </h3>
            <a href="{{ route('petugas.sirkulasi.riwayat') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Lihat Semua →
            </a>
        </div>
        
        @if($aktivitas->count() > 0)
            <div class="space-y-2">
                @foreach($aktivitas->take(5) as $log)
                    <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-2 h-2 rounded-full 
                            @if($log->status_pinjam == 'dipinjam') bg-green-500
                            @elseif($log->status_pinjam == 'terlambat') bg-red-500
                            @elseif($log->status_pinjam == 'dikembalikan') bg-blue-500
                            @else bg-gray-400 @endif">
                        </div>
                        <div class="flex-1">
                            <span class="font-medium">{{ $log->user->name ?? 'Sistem' }}</span>
                            <span class="text-gray-600">
                                @if($log->status_pinjam == 'dipinjam')
                                    meminjam buku
                                @elseif($log->status_pinjam == 'dikembalikan')
                                    mengembalikan buku
                                @elseif($log->status_pinjam == 'terlambat')
                                    terlambat mengembalikan buku
                                @else
                                    melakukan aksi pada buku
                                @endif
                            </span>
                            <span class="font-medium">{{ $log->buku->judul ?? '-' }}</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Real-time Clock
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('id-ID', { 
        hour12: false,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const clockEl = document.getElementById('realtime-clock');
    if (clockEl) clockEl.textContent = timeString;
}
setInterval(updateClock, 1000);
updateClock();

// Grafik Peminjaman & Kunjungan
@if(!empty($grafikPeminjaman['data']) || !empty($grafikKunjungan['data']))
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('chartAktivitas');
    if (!ctx) return;
    
    const labels = {!! json_encode($grafikPeminjaman['labels'] ?? $grafikKunjungan['labels'] ?? []) !!};
    const peminjamanData = {!! json_encode($grafikPeminjaman['data'] ?? array_fill(0, 7, 0)) !!};
    const kunjunganData = {!! json_encode($grafikKunjungan['data'] ?? array_fill(0, 7, 0)) !!};
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Peminjaman',
                    data: peminjamanData,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Kunjungan',
                    data: kunjunganData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} kali` } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, title: { display: true, text: 'Jumlah' } },
                x: { grid: { display: false }, title: { display: true, text: 'Tanggal' } }
            }
        }
    });
});
@endif
</script>
@endsection