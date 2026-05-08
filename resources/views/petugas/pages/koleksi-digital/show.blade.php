@extends('petugas.layouts.app')

@section('title', 'Detail Koleksi Digital')

@section('content')
<div class="p-4 md:p-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.koleksi-digital.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Koleksi Digital</h1>
                <p class="text-sm text-gray-500 mt-1">Informasi lengkap koleksi digital</p>
            </div>
        </div>
    </div>

    {{-- Status Banner --}}
    @php
        if ($buku->bisa_langsung_download) {
            $bannerClass = 'bg-blue-50 border-blue-200 text-blue-800';
            $bannerIcon = '⬇️';
            $bannerText = 'Download Bebas';
            $bannerSubtext = 'Koleksi ini dapat langsung di-download tanpa pinjam';
        } else {
            $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
            $bannerClass = $tersedia > 0 ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
            $bannerIcon = $tersedia > 0 ? '✅' : '⚠️';
            $bannerText = $tersedia > 0 ? 'Lisensi Tersedia' : 'Semua Lisensi Sedang Dipinjam';
            $bannerSubtext = $tersedia . ' dari ' . $buku->jumlah_lisensi . ' lisensi tersedia';
        }
    @endphp

    <div class="{{ $bannerClass }} border rounded-xl p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">{{ $bannerIcon }}</span>
            <div>
                <p class="font-medium">{{ $bannerText }}</p>
                <p class="text-sm opacity-80">{{ $bannerSubtext }}</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            @if($buku->bisa_langsung_download)
                <a href="{{ route('petugas.koleksi-digital.download', $buku->id) }}" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </a>
            @endif
            
            <a href="{{ route('petugas.koleksi-digital.baca', $buku->id) }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2"
               target="_blank">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Baca Online
            </a>
            
            <a href="{{ route('petugas.koleksi-digital.edit', $buku->id) }}" 
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </a>
            
            <form action="{{ route('petugas.koleksi-digital.destroy', $buku->id) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Yakin ingin menghapus koleksi digital ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column - Cover & File Info --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Cover --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="aspect-[2/3] bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg overflow-hidden">
                    @if($buku->cover_path && Storage::disk('public')->exists($buku->cover_path))
                        <img src="{{ asset('storage/' . $buku->cover_path) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover">
                    @elseif($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                        <img src="{{ asset('storage/' . $buku->sampul) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- File Info --}}
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Jenis Koleksi</span>
                        <span class="font-medium">
                            @if($buku->jenis_koleksi)
                                {!! $buku->jenis_koleksi_badge !!}
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">📚 E-Book</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Format File</span>
                        <span class="font-mono font-bold text-indigo-600">{{ strtoupper($buku->format ?? 'PDF') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Ukuran File</span>
                        <span class="font-mono">{{ $buku->formatted_file_size }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Tipe Akses</span>
                        <span class="capitalize">{{ str_replace('_', ' ', $buku->akses_digital) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Level Akses</span>
                        <span class="capitalize">
                            @if($buku->access_level == 'public')
                                🌐 Public
                            @elseif($buku->access_level == 'member_only')
                                🔒 Anggota
                            @else
                                ⭐ Premium
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Detail Info --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Informasi Buku --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Buku
                </h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Judul</p>
                        <p class="font-medium">{{ $buku->judul }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pengarang</p>
                        <p class="font-medium">{{ $buku->pengarang ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Penerbit</p>
                        <p class="font-medium">{{ $buku->penerbit ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tahun Terbit</p>
                        <p class="font-medium">{{ $buku->tahun_terbit ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">ISBN</p>
                        <p class="font-mono">{{ $buku->isbn ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Kategori</p>
                        <p class="font-medium">{{ $buku->kategori->nama ?? '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500">Deskripsi</p>
                        <p class="text-sm">{{ $buku->deskripsi ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Informasi Lisensi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Informasi Lisensi
                </h2>
                
                @if($buku->bisa_langsung_download)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                        <p class="text-blue-800">
                            <span class="font-semibold">⬇️ Koleksi Download Bebas</span><br>
                            Koleksi ini dapat langsung di-download tanpa perlu pinjam.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-indigo-50 p-3 rounded-lg text-center">
                            <p class="text-2xl font-bold text-indigo-600">{{ $buku->jumlah_lisensi }}</p>
                            <p class="text-xs text-gray-600">Total Lisensi</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $buku->lisensi_dipinjam }}</p>
                            <p class="text-xs text-gray-600">Sedang Dipinjam</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-center">
                            @php $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam; @endphp
                            <p class="text-2xl font-bold text-blue-600">{{ $tersedia }}</p>
                            <p class="text-xs text-gray-600">Tersedia</p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $buku->durasi_pinjam_hari }}</p>
                            <p class="text-xs text-gray-600">Durasi (Jam)</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Penerbit Lisensi</p>
                            <p class="font-medium">{{ $buku->penerbit_lisensi ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Berlaku</p>
                            <p class="font-medium">{{ $buku->tanggal_berlaku_lisensi ? \Carbon\Carbon::parse($buku->tanggal_berlaku_lisensi)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tanggal Kadaluarsa</p>
                            <p class="font-medium">{{ $buku->tanggal_kadaluarsa_lisensi ? \Carbon\Carbon::parse($buku->tanggal_kadaluarsa_lisensi)->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Catatan Lisensi</p>
                            <p class="text-sm">{{ $buku->catatan_lisensi ?? '-' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Daftar Peminjaman Aktif (hanya untuk ebook) --}}
            @if(!$buku->bisa_langsung_download && $peminjamanAktif->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Peminjaman Aktif
                </h2>
                
                <div class="space-y-3">
                    @foreach($peminjamanAktif as $pinjam)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">{{ $pinjam->user->name }}</p>
                            <p class="text-xs text-gray-500">No. Anggota: {{ $pinjam->user->no_anggota ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm">Expired: {{ \Carbon\Carbon::parse($pinjam->tanggal_expired)->format('d/m/Y H:i') }}</p>
                            <p class="text-xs {{ $pinjam->isExpired() ? 'text-red-500' : 'text-green-500' }}">
                                {{ $pinjam->sisaWaktuFormatted() }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection