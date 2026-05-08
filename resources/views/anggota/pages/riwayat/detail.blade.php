@extends('anggota.layouts.app')

@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('anggota.riwayat.index') }}" 
           class="inline-flex items-center gap-2 text-gray-600 hover:text-indigo-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

    {{-- Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-200 text-sm">Kode Peminjaman</p>
                    <h2 class="text-white text-xl font-bold">{{ $peminjaman->kode_pinjam ?? 'PJM-' . str_pad($peminjaman->id, 6, '0', STR_PAD_LEFT) }}</h2>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'dipinjam' => 'bg-yellow-500',
                            'terlambat' => 'bg-red-500',
                            'dikembalikan' => 'bg-green-500',
                            'menunggu' => 'bg-blue-500'
                        ];
                        $statusText = [
                            'dipinjam' => 'Sedang Dipinjam',
                            'terlambat' => 'Terlambat',
                            'dikembalikan' => 'Dikembalikan',
                            'menunggu' => 'Menunggu Verifikasi'
                        ];
                    @endphp
                    <span class="px-4 py-2 {{ $statusColors[$peminjaman->status_pinjam] ?? 'bg-gray-500' }} text-white rounded-full text-sm font-medium">
                        {{ $statusText[$peminjaman->status_pinjam] ?? ucfirst($peminjaman->status_pinjam) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            {{-- Info Peminjaman --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Informasi Peminjaman</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Pinjam</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d F Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Jatuh Tempo</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d F Y') }}</span>
                        </div>
                        @if($peminjaman->tanggal_pengembalian)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Kembali</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d F Y, H:i') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">Denda</span>
                            <span class="font-bold {{ ($peminjaman->denda_total ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                Rp {{ number_format($peminjaman->denda_total ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Informasi Buku</h3>
                    <div class="flex gap-4">
                        {{-- Cover Buku --}}
                        <div class="w-20 h-28 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            @if($peminjaman->buku && $peminjaman->buku->sampul)
                                <img src="{{ asset('storage/' . $peminjaman->buku->sampul) }}" 
                                     alt="{{ $peminjaman->buku->judul }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Info Buku --}}
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $peminjaman->buku->judul ?? 'Buku tidak ditemukan' }}</h4>
                            <p class="text-sm text-gray-500">{{ $peminjaman->buku->pengarang ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $peminjaman->buku->penerbit ?? '-' }}</p>
                            @if($peminjaman->buku && $peminjaman->buku->isbn)
                            <p class="text-xs text-gray-400 mt-1">ISBN: {{ $peminjaman->buku->isbn }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Pengembalian (BUKAN Tombol Aksi) --}}
    @if(in_array($peminjaman->status_pinjam, ['dipinjam', 'terlambat']))
    <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-6">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-medium text-blue-800">⏳ Buku Sedang Dipinjam</p>
                <p class="text-sm text-blue-700">Silakan kembalikan buku ke petugas perpustakaan. Pengembalian mandiri tidak tersedia.</p>
                @if($peminjaman->status_pinjam == 'terlambat')
                    <p class="text-xs text-red-600 mt-1">⚠️ Buku sudah melewati batas waktu pengembalian. Segera kembalikan ke petugas.</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Timeline --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Peminjaman</h3>
        
        <div class="relative">
            {{-- Garis vertikal --}}
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            
            <div class="space-y-6">
                {{-- Event 1: Peminjaman --}}
                <div class="relative flex gap-4">
                    <div class="relative z-10">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="font-medium text-blue-800">Buku Dipinjam</p>
                            <p class="text-sm text-blue-600">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d F Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Event 2: Jatuh Tempo --}}
                <div class="relative flex gap-4">
                    <div class="relative z-10">
                        <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <p class="font-medium text-yellow-800">Jatuh Tempo</p>
                            <p class="text-sm text-yellow-600">{{ \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d F Y') }}</p>
                            @if(now()->gt($peminjaman->tgl_jatuh_tempo) && $peminjaman->status_pinjam != 'dikembalikan')
                            <p class="text-xs text-red-600 mt-1">⚠️ Melebihi batas waktu</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Event 3: Pengembalian (jika sudah) --}}
                @if($peminjaman->tanggal_pengembalian)
                <div class="relative flex gap-4">
                    <div class="relative z-10">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="font-medium text-green-800">Buku Dikembalikan</p>
                            <p class="text-sm text-green-600">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d F Y, H:i') }}</p>
                            @php
                                $telat = \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->gt(\Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo));
                            @endphp
                            @if($telat)
                                <p class="text-xs text-red-600 mt-1">⚠️ Terlambat {{ \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->diffInDays(\Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)) }} hari</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection