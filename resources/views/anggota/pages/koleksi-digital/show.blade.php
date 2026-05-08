@extends('anggota.layouts.app')

@section('title', $buku->judul)
@section('page-title', 'Detail Koleksi')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-4">
        <a href="{{ route('anggota.koleksi-digital.index') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center gap-1 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Koleksi Digital
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Left Column: Cover --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sticky top-20">
                <div class="aspect-[2/3] bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg overflow-hidden mb-4">
                    @php
                        $coverFound = false;
                    @endphp
                    
                    @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                        <img src="{{ asset('storage/'.$buku->sampul) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @elseif($buku->cover_path && Storage::disk('public')->exists($buku->cover_path))
                        <img src="{{ asset('storage/'.$buku->cover_path) }}" 
                             alt="{{ $buku->judul }}"
                             class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @endif
                    
                    @if(!$coverFound)
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Status Box --}}
                <div class="bg-gray-50 rounded-lg p-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jenis:</span>
                        <span class="font-medium">{!! $buku->jenis_koleksi_badge !!}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Format:</span>
                        <span class="font-medium">{{ strtoupper($buku->format ?? 'PDF') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ukuran:</span>
                        <span class="font-medium">{{ $buku->formatted_file_size }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ketersediaan:</span>
                        @if($buku->bisa_langsung_download)
                            <span class="text-blue-600 font-medium">⬇️ Download Bebas</span>
                        @elseif($ketersediaan['bisa_dipinjam'])
                            <span class="text-green-600 font-medium">Tersedia ({{ $ketersediaan['tersedia'] }})</span>
                        @else
                            <span class="text-red-600 font-medium">Tidak Tersedia</span>
                        @endif
                    </div>
                </div>

                {{-- Action Button --}}
                @if($buku->bisa_langsung_download)
                    {{-- Koleksi Download Bebas --}}
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('anggota.koleksi-digital.download', $buku->id) }}" 
                           class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-medium transition-colors">
                            ⬇️ Download
                        </a>
                        <a href="{{ route('anggota.koleksi-digital.baca', $buku->id) }}" 
                           target="_blank"
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center py-3 rounded-lg font-medium transition-colors">
                            👁️ Baca Online
                        </a>
                    </div>
                @elseif($sedangDipinjam)
                    {{-- Sedang Meminjam E-Book --}}
                    <a href="{{ route('anggota.koleksi-digital.baca', $buku->id) }}" 
                       target="_blank"
                       class="block mt-4 w-full bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        📖 Baca E-Book
                    </a>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Sisa waktu: {{ $sedangDipinjam->sisaWaktuFormatted() }}
                    </p>
                @elseif($ketersediaan['bisa_dipinjam'])
                    {{-- E-Book Tersedia --}}
                    <form action="{{ route('anggota.koleksi-digital.pinjam', $buku->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-lg font-medium transition-colors">
                            📥 Pinjam E-Book
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Masa pinjam: {{ $buku->durasi_pinjam_label }}
                    </p>
                @else
                    {{-- E-Book Habis --}}
                    <button disabled 
                            class="mt-4 w-full bg-gray-300 text-gray-500 py-3 rounded-lg font-medium cursor-not-allowed">
                        ⏳ Sedang Dipinjam (Habis)
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-2">
                        Silakan coba lagi nanti
                    </p>
                @endif
            </div>
        </div>

        {{-- Right Column: Detail Info --}}
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $buku->judul }}</h1>
                
                @if($buku->pengarang)
                <p class="text-lg text-gray-600 mb-4">{{ $buku->pengarang }}</p>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6 text-sm">
                    @if($buku->penerbit)
                    <div>
                        <p class="text-xs text-gray-500">Penerbit</p>
                        <p class="font-medium">{{ $buku->penerbit }}</p>
                    </div>
                    @endif
                    
                    @if($buku->tahun_terbit)
                    <div>
                        <p class="text-xs text-gray-500">Tahun Terbit</p>
                        <p class="font-medium">{{ $buku->tahun_terbit }}</p>
                    </div>
                    @endif
                    
                    @if($buku->isbn)
                    <div>
                        <p class="text-xs text-gray-500">ISBN</p>
                        <p class="font-mono text-sm">{{ $buku->isbn }}</p>
                    </div>
                    @endif
                    
                    @if($buku->jumlah_halaman)
                    <div>
                        <p class="text-xs text-gray-500">Jumlah Halaman</p>
                        <p class="font-medium">{{ $buku->jumlah_halaman }} hlm</p>
                    </div>
                    @endif
                    
                    @if($buku->bahasa)
                    <div>
                        <p class="text-xs text-gray-500">Bahasa</p>
                        <p class="font-medium">{{ $buku->bahasa }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <p class="text-xs text-gray-500">Kategori</p>
                        <p class="font-medium">{{ $buku->kategori->nama ?? 'Umum' }}</p>
                    </div>
                </div>

                @if($buku->deskripsi)
                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-2">Deskripsi</h3>
                    <p class="text-gray-600 leading-relaxed">{{ $buku->deskripsi }}</p>
                </div>
                @endif
            </div>

            {{-- Info Lisensi (hanya untuk ebook) --}}
            @if(!$buku->bisa_langsung_download)
            <div class="bg-gray-50 rounded-xl p-4">
                <h3 class="font-semibold mb-2">Informasi Lisensi</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Total Lisensi</p>
                        <p class="font-medium text-lg">{{ $buku->jumlah_lisensi }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Sedang Dipinjam</p>
                        <p class="font-medium text-lg">{{ $buku->lisensi_dipinjam }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tersedia</p>
                        <p class="font-medium text-lg text-green-600">{{ $ketersediaan['tersedia'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Durasi Pinjam</p>
                        <p class="font-medium text-lg">{{ $buku->durasi_pinjam_label }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection