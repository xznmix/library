@extends('petugas.layouts.app')

@section('title', 'Koleksi Digital')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Koleksi Digital
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola e-book, bank soal, modul, dan dokumen digital</p>
            </div>
            
            <a href="{{ route('petugas.koleksi-digital.create') }}" 
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Koleksi</span>
            </a>
        </div>

        {{-- Statistik --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-indigo-600 text-2xl font-bold">{{ $statistik['total'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Total</div>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-xl shadow-sm border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-blue-600 text-2xl font-bold">{{ $statistik['ebook'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">📚 E-Book</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-green-600 text-2xl font-bold">{{ $statistik['soal'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">📝 Bank Soal</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-xl shadow-sm border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-purple-600 text-2xl font-bold">{{ $statistik['modul'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">📖 Modul</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 p-4 rounded-xl shadow-sm border border-orange-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-orange-600 text-2xl font-bold">{{ $statistik['dokumen'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">📄 Dokumen</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100 md:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-green-600 text-2xl font-bold">{{ $statistik['tersedia'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Lisensi Tersedia</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-100 md:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-yellow-600 text-2xl font-bold">{{ $statistik['dipinjam'] ?? 0 }}</div>
                        <div class="text-xs text-gray-500">Sedang Dipinjam</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-600 text-2xl font-bold">{{ $koleksi->total() }}</div>
                        <div class="text-xs text-gray-500">Halaman Ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('petugas.koleksi-digital.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul atau pengarang..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all">
                </div>
            </div>
            
            <select name="jenis" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua Jenis</option>
                <option value="ebook" {{ request('jenis') == 'ebook' ? 'selected' : '' }}>📚 E-Book</option>
                <option value="soal" {{ request('jenis') == 'soal' ? 'selected' : '' }}>📝 Bank Soal</option>
                <option value="modul" {{ request('jenis') == 'modul' ? 'selected' : '' }}>📖 Modul</option>
                <option value="dokumen" {{ request('jenis') == 'dokumen' ? 'selected' : '' }}>📄 Dokumen</option>
            </select>
            
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
            
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter
            </button>
            
            @if(request()->anyFilled(['search', 'jenis', 'status']))
                <a href="{{ route('petugas.koleksi-digital.index') }}" 
                   class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($koleksi as $buku)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all group">
                {{-- Cover Area --}}
                <div class="relative h-48 bg-gradient-to-br from-purple-500 to-indigo-600">
                    @php
                        $coverFound = false;
                    @endphp
                    
                    @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                        <img src="{{ asset('storage/' . $buku->sampul) }}" 
                            alt="{{ $buku->judul }}"
                            class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @elseif($buku->cover_path && Storage::disk('public')->exists($buku->cover_path))
                        <img src="{{ asset('storage/' . $buku->cover_path) }}" 
                            alt="{{ $buku->judul }}"
                            class="w-full h-full object-cover">
                        @php $coverFound = true; @endphp
                    @endif
                    
                    @if(!$coverFound)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-30" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Badge Jenis Koleksi --}}
                    <div class="absolute top-2 left-2">
                        @php
                            $jenisInfo = $buku->jenis_koleksi_label ?? ['icon' => '📚', 'label' => 'E-Book', 'color' => 'bg-blue-100 text-blue-800'];
                        @endphp
                        <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-xs font-medium rounded-full shadow-sm flex items-center gap-1">
                            {!! $jenisInfo['icon'] !!} {{ $jenisInfo['label'] }}
                        </span>
                    </div>
                    
                    {{-- Badge Format --}}
                    <div class="absolute top-2 right-2">
                        <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-purple-600 text-xs font-bold rounded-full shadow-sm">
                            {{ strtoupper($buku->format ?? 'PDF') }}
                        </span>
                    </div>
                    
                    {{-- Badge Lisensi / Download --}}
                    <div class="absolute bottom-2 left-2">
                        @if($buku->jenis_koleksi === 'ebook')
                            @php
                                $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam;
                            @endphp
                            @if($tersedia > 0)
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full shadow-sm">
                                    {{ $tersedia }} Lisensi
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full shadow-sm">
                                    Habis
                                </span>
                            @endif
                        @else
                            <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full shadow-sm">
                                ⬇️ Download Bebas
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Info Buku --}}
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2" title="{{ $buku->judul }}">
                        {{ $buku->judul }}
                    </h3>
                    
                    <p class="text-sm text-gray-600 mb-2 line-clamp-1">
                        {{ $buku->pengarang ?? 'Tanpa Pengarang' }}
                    </p>
                    
                    {{-- Meta Info --}}
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                            {{ $buku->format ?? 'PDF' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            {{ $buku->formatted_file_size }}
                        </span>
                    </div>
                    
                    {{-- Progress Lisensi (hanya untuk ebook) --}}
                    @if($buku->jenis_koleksi === 'ebook')
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Lisensi</span>
                            <span class="font-medium {{ $buku->lisensi_dipinjam < $buku->jumlah_lisensi ? 'text-green-600' : 'text-red-600' }}">
                                {{ $buku->lisensi_dipinjam }}/{{ $buku->jumlah_lisensi }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            @php $persentase = ($buku->lisensi_dipinjam / max($buku->jumlah_lisensi, 1)) * 100; @endphp
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $persentase }}%"></div>
                        </div>
                    </div>
                    @else
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600">Status</span>
                            <span class="font-medium text-green-600">Selalu Tersedia</span>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Action Buttons --}}
                    <div class="flex gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('petugas.koleksi-digital.show', $buku->id) }}" 
                           class="flex-1 text-center px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm hover:bg-indigo-100 transition-colors">
                            Detail
                        </a>
                        <a href="{{ route('petugas.koleksi-digital.baca', $buku->id) }}" 
                           class="px-3 py-2 bg-green-50 text-green-600 rounded-lg text-sm hover:bg-green-100 transition-colors"
                           target="_blank"
                           title="Baca Online">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="mt-8">
            {{ $koleksi->withQueryString()->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Koleksi Digital</h3>
            <p class="text-gray-500 mb-4">Mulai tambahkan e-book, bank soal, modul, atau dokumen digital pertama Anda</p>
            <a href="{{ route('petugas.koleksi-digital.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Koleksi Digital
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
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
@endpush