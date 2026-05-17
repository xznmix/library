@extends('kepala-pustaka.layouts.app')

@section('title', 'Detail Verifikasi Denda')

@section('content')
<div class="max-w-5xl mx-auto" x-data="detailVerifikasi()">

    {{-- Header with Back Button --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('kepala-pustaka.verifikasi.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Verifikasi Denda</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Informasi lengkap transaksi denda
                </p>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    <div class="mb-6 p-4 rounded-xl border transition-all
        @if($denda->status_verifikasi == 'pending')
            bg-yellow-50 border-yellow-200
        @elseif($denda->status_verifikasi == 'disetujui')
            bg-green-50 border-green-200
        @else
            bg-red-50 border-red-200
        @endif
    ">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full 
                    @if($denda->status_verifikasi == 'pending') bg-yellow-100
                    @elseif($denda->status_verifikasi == 'disetujui') bg-green-100
                    @else bg-red-100 @endif
                    flex items-center justify-center">
                    @if($denda->status_verifikasi == 'pending')
                        <svg class="w-6 h-6 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @elseif($denda->status_verifikasi == 'disetujui')
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="font-semibold 
                        @if($denda->status_verifikasi == 'pending') text-yellow-800
                        @elseif($denda->status_verifikasi == 'disetujui') text-green-800
                        @else text-red-800 @endif">
                        @if($denda->status_verifikasi == 'pending')
                            Menunggu Verifikasi
                        @elseif($denda->status_verifikasi == 'disetujui')
                            Denda Disetujui
                        @else
                            Denda Ditolak
                        @endif
                    </h3>
                    @if($denda->status_verifikasi != 'pending')
                    <p class="text-sm 
                        @if($denda->status_verifikasi == 'disetujui') text-green-600
                        @else text-red-600 @endif">
                        Diverifikasi oleh {{ $denda->diverifikasiOleh->name ?? '-' }} pada {{ $denda->diverifikasi_at ? $denda->diverifikasi_at->format('d/m/Y H:i') : '-' }}
                    </p>
                    @else
                    <p class="text-sm text-yellow-600">
                        Diajukan oleh {{ $denda->petugas->name ?? '-' }} pada {{ $denda->created_at->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
            </div>
            
            @if($denda->status_verifikasi == 'pending')
                <button @click="openVerifikasiModal({{ $denda->id }}, {{ $denda->denda_total }})"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Verifikasi Sekarang
                </button>
            @endif
        </div>
        
        @if($denda->status_verifikasi == 'ditolak' && $denda->catatan_verifikasi)
            <div class="mt-3 p-3 bg-white rounded-lg border border-red-200">
                <p class="text-sm text-red-700">
                    <span class="font-medium">Catatan Penolakan:</span> {{ $denda->catatan_verifikasi }}
                </p>
            </div>
        @endif
    </div>

    {{-- Quick Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Keterlambatan</p>
            <p class="text-xl font-bold text-red-600">
                @php
                    $jatuhTempo = \Carbon\Carbon::parse($denda->tgl_jatuh_tempo);
                    $kembali = \Carbon\Carbon::parse($denda->tanggal_pengembalian);
                    $terlambat = $kembali->diffInDays($jatuhTempo);
                @endphp
                {{ $terlambat }} Hari
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Denda per Hari</p>
            <p class="text-xl font-bold text-indigo-600">
                Rp {{ number_format($denda->denda / max($terlambat, 1), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Total Denda</p>
            <p class="text-xl font-bold text-orange-600">
                Rp {{ number_format($denda->denda_total, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs text-gray-500">Kode Eksemplar</p>
            <p class="text-xl font-mono font-bold text-gray-800">{{ $denda->kode_eksemplar ?? '-' }}</p>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Informasi Transaksi --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Info Anggota --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Informasi Anggota
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Nama</p>
                        <p class="font-medium">{{ $denda->anggota->name ?? $denda->peminjaman?->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">No. Anggota</p>
                        <p class="font-mono">{{ $denda->anggota->no_anggota ?? $denda->peminjaman?->user?->no_anggota ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Jenis</p>
                        <p>{{ ucfirst($denda->anggota->jenis ?? $denda->peminjaman?->user?->jenis ?? 'Umum') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kelas/Jurusan</p>
                        <p>{{ $denda->user->kelas ?? '-' }} {{ $denda->user->jurusan ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Info Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Informasi Buku
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Judul</p>
                        <p class="font-medium">{{ $denda->buku->judul }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pengarang</p>
                        <p>{{ $denda->buku->pengarang ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">ISBN</p>
                        <p>{{ $denda->buku->isbn ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Penerbit</p>
                        <p>{{ $denda->buku->penerbit ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tahun</p>
                        <p>{{ $denda->buku->tahun_terbit ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Timeline Peminjaman --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Timeline Peminjaman
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Tanggal Pinjam</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($denda->tanggal_pinjam)->format('d F Y') }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($denda->tanggal_pinjam)->diffForHumans() }}</span>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Jatuh Tempo</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($denda->tgl_jatuh_tempo)->format('d F Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Tanggal Kembali</p>
                            <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($denda->tanggal_pengembalian)->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Detail Denda --}}
        <div class="space-y-4">
            {{-- Card Denda --}}
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-5 text-white">
                <h3 class="font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Detail Denda
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-indigo-200">Denda Terlambat</span>
                        <span class="font-bold">Rp {{ number_format($denda->denda, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-indigo-200">Denda Rusak</span>
                        <span class="font-bold">Rp {{ number_format($denda->denda_rusak, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-indigo-400 my-2 pt-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($denda->denda_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Kondisi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Kondisi Buku
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kondisi Kembali</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($denda->kondisi_kembali == 'baik') bg-green-100 text-green-800
                            @elseif($denda->kondisi_kembali == 'rusak_ringan') bg-yellow-100 text-yellow-800
                            @elseif($denda->kondisi_kembali == 'rusak_berat') bg-orange-100 text-orange-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $denda->kondisi_kembali)) }}
                        </span>
                    </div>
                    
                    @if($denda->catatan_kondisi)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Catatan Petugas</p>
                        <div class="text-sm bg-gray-50 p-3 rounded-lg border-l-4 border-indigo-400">
                            {{ $denda->catatan_kondisi }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Info Petugas --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Petugas Pencatat
                </h3>
                
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-lg font-bold text-indigo-600">
                            {{ substr($denda->petugas->name ?? 'P', 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800">{{ $denda->petugas->name ?? 'Tidak diketahui' }}</p>
                        <p class="text-xs text-gray-500">{{ $denda->petugas->role ?? 'Petugas' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $denda->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-2">
                <a href="{{ route('kepala-pustaka.verifikasi.index') }}" 
                   class="flex-1 px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-center">
                    Kembali
                </a>
                @if($denda->status_verifikasi == 'pending')
                <button onclick="window.print()" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Print
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL VERIFIKASI (DITAMBAHKAN LANGSUNG DI DETAIL) --}}
<div x-show="verifikasiModal" 
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
             @click="verifikasiModal = false"></div>
        
        <div class="relative bg-white rounded-xl max-w-md w-full p-6 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Verifikasi Denda</h3>
                    <p class="text-sm text-gray-500">Setujui atau tolak denda ini</p>
                </div>
                <button @click="verifikasiModal = false" class="ml-auto text-gray-400 hover:text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form @submit.prevent="submitVerifikasi">
                @csrf
                <input type="hidden" name="id" x-model="selectedId">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 p-3 border rounded-lg flex-1 cursor-pointer"
                                   :class="{ 'border-green-500 bg-green-50': status == 'disetujui' }">
                                <input type="radio" name="status" value="disetujui" x-model="status" class="text-green-600">
                                <span class="text-sm text-gray-700">Setujui</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border rounded-lg flex-1 cursor-pointer"
                                   :class="{ 'border-red-500 bg-red-50': status == 'ditolak' }">
                                <input type="radio" name="status" value="ditolak" x-model="status" class="text-red-600">
                                <span class="text-sm text-gray-700">Tolak</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="status == 'disetujui'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nominal Disetujui (opsional)
                        </label>
                        <input type="number" x-model="nominalSetuju"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               :max="maxDenda">
                        <p class="text-xs text-gray-500 mt-1" x-text="'Kosongkan jika sesuai dengan denda asli (Rp ' + numberFormat(maxDenda) + ')'"></p>
                    </div>
                    
                    <div x-show="status == 'ditolak'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="catatan" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                  placeholder="Alasan penolakan..."></textarea>
                    </div>
                    
                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" @click="verifikasiModal = false" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Batal
                        </button>
                        <button type="submit" :disabled="loading"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2">
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
function detailVerifikasi() {
    return {
        // Modal state
        verifikasiModal: false,
        loading: false,
        selectedId: null,
        maxDenda: 0,
        status: 'disetujui',
        nominalSetuju: '',
        catatan: '',
        
        openVerifikasiModal(id, maxDenda) {
            this.selectedId = id;
            this.maxDenda = maxDenda;
            this.nominalSetuju = maxDenda;
            this.catatan = '';
            this.status = 'disetujui';
            this.verifikasiModal = true;
        },
        
        async submitVerifikasi() {
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', this.status);
                if (this.status == 'disetujui' && this.nominalSetuju && this.nominalSetuju != this.maxDenda) {
                    formData.append('nominal_setuju', this.nominalSetuju);
                }
                if (this.status == 'ditolak') {
                    formData.append('catatan', this.catatan);
                }
                
                const response = await fetch(`/kepala-pustaka/verifikasi/${this.selectedId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        confirmButtonColor: '#4f46e5'
                    }).then(() => {
                        window.location.href = '{{ route("kepala-pustaka.verifikasi.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: result.message,
                        confirmButtonColor: '#4f46e5'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error.message,
                    confirmButtonColor: '#4f46e5'
                });
            } finally {
                this.loading = false;
                this.verifikasiModal = false;
            }
        },
        
        numberFormat(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>
@endsection