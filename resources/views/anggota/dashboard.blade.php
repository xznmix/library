@extends('anggota.layouts.app')

@section('title', 'Dashboard Anggota')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Header Selamat Datang --}}
    <div class="bg-biru rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
                <p class="text-blue-100">Nikmati kemudahan akses koleksi perpustakaan digital SMAN 1 Tambang</p>
            </div>
            <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- STATISTIK CEPAT --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-biru-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
                <span class="text-xs bg-biru-50 text-biru px-2 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Sedang Dipinjam</p>
            <p class="text-3xl font-bold text-gray-800">{{ $sedang_dipinjam ?? 0 }}</p>
            <a href="{{ route('anggota.riwayat.index', ['status' => 'dipinjam']) }}" 
               class="mt-3 text-sm text-biru hover:text-biru-dark flex items-center gap-1">
                Lihat Detail
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-oren" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                @if(($jatuh_tempo ?? 0) > 0)
                    <span class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded-full">Perhatian!</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mb-1">Jatuh Tempo (3 hari)</p>
            <p class="text-3xl font-bold {{ ($jatuh_tempo ?? 0) > 0 ? 'text-red-600' : 'text-gray-800' }}">
                {{ $jatuh_tempo ?? 0 }}
            </p>
            <a href="{{ route('anggota.riwayat.index', ['status' => 'jatuh_tempo']) }}" 
               class="mt-3 text-sm text-oren hover:text-oren-dark flex items-center gap-1">
                Lihat Detail
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-hijau-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-hijau" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs bg-hijau-50 text-hijau px-2 py-1 rounded-full">Seumur Hidup</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Total Peminjaman</p>
            <p class="text-3xl font-bold text-gray-800">{{ $total_peminjaman ?? 0 }}</p>
            <a href="{{ route('anggota.riwayat.index') }}" 
               class="mt-3 text-sm text-hijau hover:text-hijau-dark flex items-center gap-1">
                Lihat Riwayat
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    {{-- GRID 2 KOLOM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KIRI: Rekomendasi Berdasarkan Peminjaman --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Rekomendasi Berdasarkan Peminjaman Anda
                </h2>
                <a href="{{ route('opac.index') }}" class="text-sm text-biru hover:text-biru-dark">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($rekomendasi_peminjaman ?? [] as $buku)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                    <div class="flex">
                        <div class="w-24 h-32 bg-gray-100 flex-shrink-0 relative">
                            @if($buku->sampul)
                                <img src="{{ asset('storage/'.$buku->sampul) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            @if($buku->kategori)
                            <div class="absolute bottom-2 left-2">
                                <span class="px-2 py-0.5 bg-biru text-white text-xs rounded-full">
                                    {{ $buku->kategori->nama }}
                                </span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="flex-1 p-3">
                            <h3 class="font-semibold text-gray-800 line-clamp-2 mb-1">{{ $buku->judul }}</h3>
                            <p class="text-xs text-gray-500 mb-1">{{ $buku->pengarang ?? 'Unknown' }}</p>
                            
                            {{-- BINTANG RATING --}}
                            <div class="flex items-center gap-1 mb-2">
                                @php
                                    $rating = $buku->rating ?? 0;
                                    $totalUlasan = $buku->jumlah_ulasan ?? 0;
                                @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    @if($rating >= $i)
                                        <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @elseif($rating >= $i - 0.5)
                                        <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                                <span class="text-xs text-gray-400 ml-1">({{ $totalUlasan }})</span>
                            </div>
                            
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                                <span>{{ $buku->tahun_terbit ?? '-' }}</span>
                                <span>•</span>
                                <span>{{ $buku->stok ?? 0 }} tersisa</span>
                            </div>
                            
                            {{-- ============================================= --}}
                            {{-- TOMBOL AKSI (PINJAM vs BOOKING) --}}
                            {{-- ============================================= --}}
                            <div class="flex gap-2 mt-2">
                                <a href="{{ route('opac.show', $buku->id) }}" 
                                   class="text-xs text-biru hover:text-biru-dark font-medium">Detail</a>
                                
                                @if($buku->tipe == 'digital')
                                    {{-- E-BOOK: Pinjam langsung --}}
                                    @if($buku->stok > 0)
                                        <button onclick="pinjamEbook({{ $buku->id }})" 
                                                class="text-xs bg-hijau-50 text-hijau hover:bg-hijau-100 px-2 py-1 rounded-lg transition-colors">
                                            📖 Pinjam E-Book
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400">Stok Habis</span>
                                    @endif
                                @else
                                    {{-- BUKU FISIK: Booking --}}
                                    @if($buku->stok_siap_pinjam > 0)
                                        <a href="{{ route('anggota.booking.create', $buku->id) }}" 
                                           class="text-xs bg-yellow-50 text-yellow-700 hover:bg-yellow-100 px-2 py-1 rounded-lg transition-colors">
                                            📅 Booking
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">Stok Habis</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-2 bg-gray-50 rounded-xl p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <p class="text-gray-500 mb-2">Belum ada rekomendasi</p>
                    <p class="text-sm text-gray-400">Mulai pinjam buku untuk mendapatkan rekomendasi yang sesuai dengan minat Anda</p>
                    <a href="{{ route('opac.index') }}" class="mt-3 inline-block text-biru hover:text-biru-dark text-sm">
                        Jelajahi Koleksi Buku →
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- KANAN: Poin & Aktivitas --}}
        <div class="space-y-4">
            <div class="bg-biru rounded-xl p-5 text-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Reward</span>
                </div>
                <p class="text-3xl font-bold mb-1">{{ number_format($poin_aktif ?? 0) }} POIN</p>
                <p class="text-blue-200 text-sm">Pembaca Aktif</p>
                <div class="mt-3 bg-white/10 rounded-lg p-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span>Peringkat Anda</span>
                        <div class="text-right">
                            <span class="font-bold text-lg">#{{ $peringkat ?? 'N/A' }}</span>
                            @if(isset($total_anggota) && $total_anggota > 0)
                            <span class="text-xs block">dari {{ number_format($total_anggota) }} anggota</span>
                            @endif
                        </div>
                    </div>
                    @if(isset($poin_ke_peringkat_selanjutnya) && $poin_ke_peringkat_selanjutnya > 0)
                    <div class="mt-2 pt-2 border-t border-white/20">
                        <div class="flex justify-between text-xs mb-1">
                            <span>Butuh {{ number_format($poin_ke_peringkat_selanjutnya) }} poin lagi</span>
                            <span>ke peringkat #{{ $peringkat_selanjutnya ?? ($peringkat - 1) }}</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-1">
                            <div class="bg-yellow-400 rounded-full h-1" style="width: {{ min(100, ($poin_ke_peringkat_selanjutnya > 0 ? ($poin_aktif / ($poin_aktif + $poin_ke_peringkat_selanjutnya) * 100) : 100)) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Aktivitas Terakhir
                </h3>
                
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($aktivitas_terakhir ?? [] as $aktivitas)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 mt-1.5 rounded-full 
                            @if($aktivitas->status_pinjam == 'dipinjam') bg-biru
                            @elseif($aktivitas->status_pinjam == 'terlambat') bg-oren
                            @else bg-hijau @endif">
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-800 font-medium">
                                {{ $aktivitas->buku->judul ?? 'Buku tidak tersedia' }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $aktivitas->created_at->isoFormat('dddd, D MMM Y HH:mm') }}
                            </p>
                            <p class="text-xs {{ $aktivitas->status_pinjam == 'dikembalikan' ? 'text-hijau' : ($aktivitas->status_pinjam == 'terlambat' ? 'text-oren' : 'text-biru') }}">
                                Status: {{ ucfirst($aktivitas->status_pinjam ?? 'Dipinjam') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas peminjaman</p>
                    @endforelse
                </div>
                
                <a href="{{ route('anggota.riwayat.index') }}" 
                   class="mt-3 text-sm text-biru hover:text-biru-dark flex items-center justify-center gap-1 pt-2 border-t">
                    Lihat Semua Aktivitas
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- REKOMENDASI BERDASARKAN BUKU TERPOPULER --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-biru" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Rekomendasi Berdasarkan Buku Terpopuler
            </h2>
            <a href="{{ route('opac.index', ['sort' => 'popular']) }}" class="text-sm text-biru hover:text-biru-dark">Lihat Semua →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @forelse($rekomendasi_populer ?? [] as $buku)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                <div class="h-40 bg-gray-100 relative">
                    @if($buku->sampul)
                        <img src="{{ asset('storage/'.$buku->sampul) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-yellow-400 text-gray-800 text-xs font-bold rounded-full shadow-md">
                            #{{ $loop->iteration }}
                        </span>
                    </div>
                    
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <a href="{{ route('opac.show', $buku->id) }}" class="bg-biru text-white px-3 py-1 rounded-lg text-sm font-medium">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                
                <div class="p-3">
                    <h3 class="font-semibold text-sm line-clamp-2 mb-1">{{ $buku->judul }}</h3>
                    <p class="text-xs text-gray-500 mb-2">{{ $buku->pengarang ?? 'Unknown Author' }}</p>
                    
                    <div class="flex items-center gap-1 mb-2">
                        @php
                            $rating = $buku->rating ?? 0;
                            $totalUlasan = $buku->jumlah_ulasan ?? 0;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($rating >= $i)
                                <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @elseif($rating >= $i - 0.5)
                                <svg class="w-3 h-3 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-3 h-3 text-gray-300 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                        <span class="text-xs text-gray-400 ml-1">({{ $totalUlasan }})</span>
                    </div>
                    
                    {{-- ============================================= --}}
                    {{-- TOMBOL AKSI (PINJAM vs BOOKING) - POPULER --}}
                    {{-- ============================================= --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-xs font-semibold text-gray-700">{{ $buku->total_dipinjam ?? 0 }}</span>
                        </div>
                        
                        @if($buku->tipe == 'digital')
                            {{-- E-BOOK: Pinjam langsung --}}
                            @if($buku->tipe == 'digital')
                                @if($buku->stok > 0)
                                    <form action="{{ route('anggota.koleksi-digital.pinjam', $buku->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-xs bg-hijau-50 text-hijau hover:bg-hijau-100 px-2 py-1 rounded-lg transition-colors"
                                                onclick="return confirm('Yakin ingin meminjam e-book ini?')">
                                            📖 Pinjam E-Book
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Stok Habis</span>
                                @endif
                            @endif
                        @else
                            {{-- BUKU FISIK: Booking --}}
                            @if($buku->stok_siap_pinjam > 0)
                                <a href="{{ route('anggota.booking.create', $buku->id) }}" 
                                   class="text-xs bg-yellow-50 text-yellow-700 hover:bg-yellow-100 px-2 py-1 rounded-lg transition-colors">
                                    📅 Booking
                                </a>
                            @else
                                <span class="text-xs text-gray-400">Stok Habis</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-gray-50 rounded-xl p-8 text-center">
                <p class="text-gray-500">Belum ada data buku populer.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.pinjamEbook = function(bukuId) {
    Swal.fire({
        title: 'Konfirmasi Peminjaman E-Book',
        text: "Apakah Anda yakin ingin meminjam e-book ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Pinjam!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // ✅ BUAT FORM POST
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/anggota/koleksi-digital/' + bukuId + '/pinjam';
            
            let csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
};
</script>
@endpush

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.max-h-64::-webkit-scrollbar {
    width: 4px;
}

.max-h-64::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.max-h-64::-webkit-scrollbar-thumb {
    background: #93C5FD;
    border-radius: 10px;
}

.max-h-64::-webkit-scrollbar-thumb:hover {
    background: #3B82F6;
}
</style>
@endsection