@extends('anggota.layouts.app')

@section('title', 'Koleksi Digital')
@section('page-title', 'Koleksi Digital')

@section('content')
<div class="space-y-6">

    {{-- Header dengan Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Total</span>
            </div>
            <p class="text-2xl font-bold">{{ $statistik['total'] ?? 0 }}</p>
            <p class="text-indigo-100 text-xs">Semua Koleksi</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">📚</span>
            </div>
            <p class="text-2xl font-bold">{{ $statistik['ebook'] ?? 0 }}</p>
            <p class="text-blue-100 text-xs">E-Book</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">📝</span>
            </div>
            <p class="text-2xl font-bold">{{ $statistik['soal'] ?? 0 }}</p>
            <p class="text-green-100 text-xs">Bank Soal</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">📖</span>
            </div>
            <p class="text-2xl font-bold">{{ $statistik['modul'] ?? 0 }}</p>
            <p class="text-purple-100 text-xs">Modul</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">📄</span>
            </div>
            <p class="text-2xl font-bold">{{ $statistik['dokumen'] ?? 0 }}</p>
            <p class="text-orange-100 text-xs">Dokumen</p>
        </div>
    </div>

    {{-- Peminjaman Aktif (Jika Ada) --}}
    @if($peminjamanAktif->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-yellow-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                E-Book yang Sedang Anda Pinjam
            </h3>
            <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">
                {{ $peminjamanAktif->count() }} Buku
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($peminjamanAktif as $pinjam)
            <div class="bg-white rounded-lg p-3 flex items-center gap-3 shadow-sm border border-yellow-100">
                <div class="w-16 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded overflow-hidden flex-shrink-0">
                    @php
                        $coverFound = false;
                    @endphp
                    @if($pinjam->buku->sampul && Storage::disk('public')->exists($pinjam->buku->sampul))
                        <img src="{{ asset('storage/'.$pinjam->buku->sampul) }}" 
                             alt="{{ $pinjam->buku->judul }}"
                             class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @elseif($pinjam->buku->cover_path && Storage::disk('public')->exists($pinjam->buku->cover_path))
                        <img src="{{ asset('storage/'.$pinjam->buku->cover_path) }}" 
                             alt="{{ $pinjam->buku->judul }}"
                             class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @endif
                    
                    @if(!$coverFound)
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-medium text-sm line-clamp-1">{{ $pinjam->buku->judul }}</h4>
                    <p class="text-xs text-gray-500 mb-1">{{ $pinjam->buku->pengarang ?? '-' }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-yellow-600">
                            ⏳ {{ $pinjam->sisaWaktuFormatted() }}
                        </span>
                        <a href="{{ route('anggota.koleksi-digital.baca', $pinjam->buku->id) }}" 
                           target="_blank"
                           class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded transition-colors">
                            Baca
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($peminjamanAktif->count() > 3)
        <div class="mt-3 text-center">
            <a href="{{ route('anggota.riwayat.index') }}" 
               class="text-sm text-yellow-700 hover:text-yellow-900">
                Lihat Semua Pinjaman →
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- Filter & Search Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('anggota.koleksi-digital.index') }}" class="flex flex-col md:flex-row gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Cari judul, pengarang, atau penerbit..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all">
            </div>
            
            <select name="jenis" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua Jenis</option>
                <option value="ebook" {{ request('jenis') == 'ebook' ? 'selected' : '' }}>📚 E-Book</option>
                <option value="soal" {{ request('jenis') == 'soal' ? 'selected' : '' }}>📝 Bank Soal</option>
                <option value="modul" {{ request('jenis') == 'modul' ? 'selected' : '' }}>📖 Modul</option>
                <option value="dokumen" {{ request('jenis') == 'dokumen' ? 'selected' : '' }}>📄 Dokumen</option>
            </select>
            
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Filter
            </button>
            
            @if(request()->anyFilled(['search', 'jenis']))
                <a href="{{ route('anggota.koleksi-digital.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Grid Koleksi Digital --}}
    @if($koleksi->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($koleksi as $buku)
            @php
                $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
                $sedangDipinjam = $peminjamanAktif->contains('buku_id', $buku->id);
                $jenisInfo = $buku->jenis_koleksi_label;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                {{-- Cover --}}
                <div class="relative aspect-[2/3] bg-gradient-to-br from-indigo-100 to-purple-100">
                    @php
                        $coverFound = false;
                    @endphp
                    
                    @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                        <img src="{{ asset('storage/'.$buku->sampul) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @php $coverFound = true; @endphp
                    @elseif($buku->cover_path && Storage::disk('public')->exists($buku->cover_path))
                        <img src="{{ asset('storage/'.$buku->cover_path) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @php $coverFound = true; @endphp
                    @endif
                    
                    @if(!$coverFound)
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Badge Jenis --}}
                    <div class="absolute top-2 left-2">
                        <span class="px-1.5 py-0.5 bg-white/90 backdrop-blur-sm text-xs font-medium rounded-full shadow-sm">
                            {!! $jenisInfo['icon'] !!}
                        </span>
                    </div>
                    
                    {{-- Badge Format --}}
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-indigo-600 text-xs font-bold rounded-full shadow-sm">
                            {{ strtoupper($buku->format ?? 'PDF') }}
                        </span>
                    </div>

                    {{-- Badge Status --}}
                    <div class="absolute bottom-2 left-2 right-2">
                        @if($buku->bisa_langsung_download)
                            <span class="px-2 py-1 bg-blue-500 text-white text-xs font-bold rounded-full shadow-sm w-full block text-center">
                                ⬇️ Download Bebas
                            </span>
                        @elseif($sedangDipinjam)
                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded-full shadow-sm w-full block text-center">
                                📖 Sedang Dipinjam
                            </span>
                        @elseif($tersedia > 0)
                            <span class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full shadow-sm w-full block text-center">
                                ✓ Tersedia ({{ $tersedia }})
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-sm w-full block text-center">
                                ✗ Habis
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Info Buku --}}
                <div class="p-3">
                    <h3 class="font-semibold text-sm line-clamp-2 mb-1" title="{{ $buku->judul }}">
                        {{ $buku->judul }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-2 line-clamp-1">{{ $buku->pengarang ?? '-' }}</p>
                    
                    {{-- Progress Bar Lisensi (hanya untuk ebook) --}}
                    @if(!$buku->bisa_langsung_download && $buku->jumlah_lisensi > 1)
                    <div class="mb-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">Lisensi</span>
                            <span class="{{ $tersedia > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tersedia }}/{{ $buku->jumlah_lisensi }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            @php $persen = ($buku->lisensi_dipinjam / $buku->jumlah_lisensi) * 100; @endphp
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Action Button --}}
                    <a href="{{ route('anggota.koleksi-digital.show', $buku->id) }}" 
                       class="mt-2 w-full block text-center text-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-600 py-1.5 rounded-lg transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $koleksi->withQueryString()->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Koleksi</h3>
            <p class="text-gray-500 mb-4">
                @if(request()->anyFilled(['search', 'jenis']))
                    Tidak ada koleksi yang sesuai dengan filter
                @else
                    Saat ini belum tersedia koleksi digital
                @endif
            </p>
            @if(request()->anyFilled(['search', 'jenis']))
                <a href="{{ route('anggota.koleksi-digital.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset Filter
                </a>
            @endif
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection