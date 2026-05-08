@extends('opac.layouts.app')

@section('title', 'OPAC - Pencarian Koleksi Cerdas dengan AI')

@section('content')

{{-- Hero Section dengan AI Highlight Super Besar --}}
<div class="relative overflow-hidden bg-gradient-to-br from-white via-biru-50/30 to-oren-50/30">
    <div class="absolute top-0 right-0 w-96 h-96 bg-oren/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-biru/5 rounded-full blur-3xl"></div>
    
    <div class="max-w-7xl mx-auto px-4 py-16 md:py-24 relative">
        
        {{-- AI Super Badge --}}
        <div class="flex justify-center mb-6" data-aos="fade-down">
            <div class="inline-flex items-center gap-3 bg-gradient-to-r from-oren to-oren-dark text-white px-5 py-2 rounded-full shadow-lg">
                <div class="relative">
                    <div class="w-3 h-3 bg-white rounded-full animate-ping absolute"></div>
                    <div class="w-3 h-3 bg-white rounded-full relative"></div>
                </div>
                <span class="text-oren font-bold tracking-wide">🤖 AI SUPER INTELLIGENCE</span>
                <span class="text-xs bg-black/20 px-2 py-0.5 rounded-full">Powered by Gemini</span>
            </div>
        </div>
        
        <h1 class="text-4xl md:text-6xl font-bold text-center mb-6" data-aos="fade-up">
            <span class="text-hitam">Temukan Koleksi</span>
            <span class="text-oren">Favoritmu</span>
            <br>
            <span class="text-3xl md:text-4xl text-biru">dengan Kecerdasan Buatan</span>
        </h1>
        
        <p class="text-center text-hitam/70 text-lg mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            Lebih dari 10.000+ koleksi buku fisik, e-book, modul, dan bank soal. 
            Cukup tanyakan ke AI, kami akan temukan yang terbaik untukmu!
        </p>
        
        {{-- AI SEARCH BOX SUPER BESAR --}}
        <div class="max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            <form action="{{ route('opac.index') }}" method="GET" id="searchForm">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-oren/20 via-biru/20 to-oren/20 rounded-2xl blur-xl opacity-50"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-transparent hover:border-oren/30 transition-all duration-300">
                        <div class="flex flex-col md:flex-row">
                            <div class="flex-1 flex items-center px-5 py-4">
                                <i class="fas fa-robot text-oren text-xl mr-3"></i>
                                <input type="text" 
                                    name="q" 
                                    id="searchInput"
                                    value="{{ request('q', $search_query ?? '') }}"
                                    placeholder="Tanyakan ke AI... Contoh: 'Rekomendasikan buku tentang motivasi hidup' atau 'Buku pelajaran matematika kelas 10'"
                                    class="w-full focus:outline-none text-hitam placeholder-gray-400 text-base md:text-lg"
                                    autocomplete="off">
                            </div>
                            <div class="flex border-t md:border-t-0 md:border-l border-gray-100">
                                <button type="submit" 
                                        id="searchButton"
                                        class="bg-gradient-to-r from-biru to-biru-dark hover:from-biru-dark hover:to-biru text-white px-8 py-4 font-semibold transition-all duration-300 flex items-center justify-center gap-2 md:rounded-r-2xl">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Cari Sekarang</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            {{-- AI Toggle + Fitur Cepat --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                {{-- AI Toggle Switch --}}
                <label class="inline-flex items-center cursor-pointer group">
                    <input type="checkbox" 
                        name="use_ai" 
                        id="useAI" 
                        value="1"
                        {{ request('use_ai', $is_ai_search ?? false) ? 'checked' : '' }}
                        class="sr-only peer"
                        onchange="toggleAISearch()">
                    <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-oren rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-oren"></div>
                    <div class="ml-3 flex items-center gap-2">
                        <i class="fas fa-microchip text-oren text-sm"></i>
                        <span class="text-sm font-medium text-hitam group-hover:text-oren transition-colors">
                            Mode AI Cerdas
                        </span>
                        <span class="text-xs bg-oren/10 text-oren px-2 py-0.5 rounded-full">Beta</span>
                    </div>
                </label>
                
                {{-- Quick Filter Chips --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => null]) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ !request('tipe') ? 'bg-biru text-white shadow-md' : 'bg-gray-100 text-hitam hover:bg-gray-200' }}">
                        Semua
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => 'fisik']) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ request('tipe') == 'fisik' ? 'bg-biru text-white shadow-md' : 'bg-gray-100 text-hitam hover:bg-gray-200' }}">
                        📖 Buku Fisik
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['tipe' => 'digital']) }}" 
                       class="px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ request('tipe') == 'digital' ? 'bg-biru text-white shadow-md' : 'bg-gray-100 text-hitam hover:bg-gray-200' }}">
                        💻 E-Book & Modul
                    </a>
                </div>
            </div>
            
            {{-- AI Active Info --}}
            @if(request('use_ai') || ($is_ai_search ?? false))
            <div class="mt-4 text-center animate-pulse">
                <div class="inline-flex items-center gap-2 bg-oren/10 backdrop-blur-sm px-5 py-2 rounded-full border border-oren/20">
                    <div class="relative">
                        <div class="w-2 h-2 bg-green-500 rounded-full absolute animate-ping"></div>
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    </div>
                    <span class="text-sm text-hitam font-medium">🤖 AI aktif • Saya bisa memahami bahasa alami, coba tanyakan seperti ke teman!</span>
                    <i class="fas fa-smile-wink text-oren"></i>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    {{-- Decorative Wave --}}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" class="w-full">
        <path fill="#FFFFFF" fill-opacity="1" d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
    </svg>
</div>

{{-- Hasil Pencarian --}}
<div class="max-w-7xl mx-auto px-4 py-12">
    
    {{-- ==================== HASIL AI ==================== --}}
    @if(isset($ai_results) && ($is_ai_search ?? false))
    <div class="space-y-8" data-aos="fade-up">
        
        {{-- AI Smart Response Card --}}
        @if(isset($ai_results['smart_response']))
        <div class="bg-gradient-to-r from-biru-50 via-white to-oren-50 rounded-2xl p-6 md:p-8 border-2 border-biru/20 shadow-xl">
            <div class="flex flex-col md:flex-row items-start gap-5">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-gradient-to-br from-oren to-oren-dark rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
                        <i class="fas fa-robot text-white text-3xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="font-bold text-biru text-lg">🤖 AI Assistant</span>
                        <span class="text-xs bg-oren/20 text-oren-dark px-2 py-1 rounded-full">Gemini AI • Real-time</span>
                        <span class="text-xs bg-biru/10 text-biru px-2 py-1 rounded-full">Semantic Search</span>
                    </div>
                    <p class="text-hitam text-base md:text-lg leading-relaxed">{{ $ai_results['smart_response'] }}</p>
                    
                    @if(isset($ai_results['suggested_rak']))
                    <div class="mt-4 flex items-center gap-3 flex-wrap">
                        <div class="bg-biru/10 rounded-full px-4 py-2 inline-flex items-center gap-2">
                            <i class="fas fa-map-pin text-oren"></i>
                            <span class="text-sm font-medium text-hitam">📍 Lokasi Rak:</span>
                            <span class="text-sm font-bold text-biru">{{ $ai_results['suggested_rak'] }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        {{-- Hasil Relevan dari AI --}}
        @if(count($ai_results['results'] ?? []) > 0)
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-hijau flex items-center justify-center">
                    <i class="fas fa-check-circle text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-hitam">Hasil Paling Relevan</h2>
                <span class="text-sm bg-oren/20 text-oren-dark px-3 py-1 rounded-full">AI Rekomendasi • {{ count($ai_results['results']) }} ditemukan</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($ai_results['results'] as $bukuAI)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group hover:-translate-y-2">
                    <div class="relative h-52 bg-gradient-to-br from-biru-50 to-oren-50">
                        @if($bukuAI['sampul'] && Storage::disk('public')->exists($bukuAI['sampul']))
                            <img src="{{ asset('storage/' . $bukuAI['sampul']) }}" 
                                alt="{{ $bukuAI['judul'] }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-book-open text-5xl text-biru/30"></i>
                            </div>
                        @endif
                        
                        {{-- Type Badge --}}
                        <div class="absolute top-3 right-3">
                            <span class="px-2.5 py-1 bg-white/95 backdrop-blur-sm rounded-lg text-xs font-semibold shadow-sm
                                {{ ($bukuAI['tipe'] ?? 'fisik') == 'digital' ? 'text-purple-600' : 'text-biru' }}">
                                {{ ($bukuAI['tipe'] ?? 'fisik') == 'digital' ? '📱 E-Book' : '📖 Buku Fisik' }}
                            </span>
                        </div>
                        
                        {{-- AI Highlight --}}
                        <div class="absolute bottom-3 left-3">
                            <span class="text-[10px] bg-gradient-to-r from-oren to-oren-dark text-white px-2 py-0.5 rounded-full shadow-md flex items-center gap-1">
                                <i class="fas fa-robot text-[8px]"></i>
                                AI Recommend
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-hitam mb-1 line-clamp-2">{{ $bukuAI['judul'] }}</h3>
                        <p class="text-sm text-hitam/60 mb-3">{{ $bukuAI['pengarang'] ?? 'Tanpa Pengarang' }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-hijau font-medium">✅ Tersedia di {{ $bukuAI['rak'] }}</span>
                            <a href="{{ route('opac.show', $bukuAI['id']) }}" 
                               class="text-biru hover:text-oren text-sm font-semibold flex items-center gap-1 transition-colors">
                                Detail <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- REKOMENDASI AI --}}
        @if(count($ai_results['recommendations'] ?? []) > 0)
        <div class="mt-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-oren flex items-center justify-center">
                    <i class="fas fa-star text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-hitam">💡 Rekomendasi AI untuk Anda</h2>
                <span class="text-sm bg-oren/20 text-oren-dark px-3 py-1 rounded-full">Mungkin Anda juga suka</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($ai_results['recommendations'] as $rec)
                <div class="bg-gradient-to-br from-oren-50 to-biru-50 rounded-xl shadow-md border border-oren/20 overflow-hidden hover:shadow-xl transition-all duration-300 group">
                    <div class="relative h-44 bg-gradient-to-br from-oren-100 to-biru-100">
                        @if(isset($rec['sampul']) && $rec['sampul'] && Storage::disk('public')->exists($rec['sampul']))
                            <img src="{{ asset('storage/' . $rec['sampul']) }}" 
                                alt="{{ $rec['judul'] ?? 'Buku' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-gem text-4xl text-oren/30"></i>
                            </div>
                        @endif
                        <div class="absolute bottom-2 left-2">
                            <span class="text-[10px] bg-white/90 backdrop-blur-sm px-2 py-0.5 rounded-full text-oren-dark font-medium">
                                <i class="fas fa-robot"></i> AI Rekomendasi
                            </span>
                        </div>
                    </div>
                    <div class="p-3">
                        <h4 class="font-bold text-hitam text-sm line-clamp-2">{{ $rec['judul'] ?? 'Rekomendasi Buku' }}</h4>
                        <p class="text-xs text-hitam/60 mt-1">{{ $rec['pengarang'] ?? 'Tim TAMBANG ILMU' }}</p>
                        <p class="text-xs text-oren mt-1 italic line-clamp-3">"{{ $rec['alasan'] ?? 'Buku ini cocok untuk Anda! Yuk segera baca dan temukan inspirasinya. 📚✨' }}"</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                            <span class="text-xs text-hitam/50">
                                <i class="fas fa-map-marker-alt"></i> {{ $rec['tempat'] ?? ($rec['rak'] ?? 'Perpustakaan Kami') }}
                            </span>
                            @if(isset($rec['id']) && $rec['id'])
                            <a href="{{ route('opac.show', $rec['id']) }}" class="text-biru text-xs hover:text-oren font-medium">Lihat →</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Kata Kunci Alternatif --}}
        @if(isset($ai_results['alternative_keywords']) && count($ai_results['alternative_keywords']) > 0)
        <div class="mt-6 p-5 bg-gradient-to-r from-yellow-50 to-oren-50 rounded-xl border border-oren/20">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-lightbulb text-oren text-xl"></i>
                <span class="font-semibold text-hitam">💡 AI Saran Kata Kunci:</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($ai_results['alternative_keywords'] as $keyword)
                <a href="{{ route('opac.ai.search') }}?q={{ urlencode($keyword) }}" 
                   class="text-sm bg-white hover:bg-oren/20 text-hitam px-4 py-2 rounded-full transition-colors shadow-sm border border-gray-200 hover:border-oren">
                    <i class="fas fa-search text-oren text-xs mr-1"></i> {{ $keyword }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- REKOMENDASI EKSTERNAL GAYA TAMBANG ILMU --}}
        @if(!empty($ai_results['recommendations']))
        <div class="mt-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-oren to-oren-dark flex items-center justify-center shadow-lg">
                    <i class="fas fa-star text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-hitam">📚 Rekomendasi dari TAMBANG ILMU</h2>
                    <p class="text-sm text-hitam/60">Aku cariin yang terbaik buat kamu, Sobat Literasi!</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($ai_results['recommendations'] as $rec)
                <div class="bg-gradient-to-r from-oren-50 to-yellow-50 rounded-2xl p-5 border border-oren/20 shadow-md hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-xl bg-white/80 backdrop-blur flex items-center justify-center shadow-sm">
                            <i class="fas fa-book-open text-2xl text-oren"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-hitam text-lg">{{ $rec['judul'] }}</h3>
                            <p class="text-sm text-hitam/60">✍️ {{ $rec['pengarang'] }}</p>
                            <p class="text-sm text-oren mt-2 italic">"{{ $rec['alasan'] }}"</p>
                            
                            @if(isset($rec['tempat']))
                            <div class="mt-3 flex items-center gap-2 text-xs text-hitam/70 bg-white/50 rounded-lg p-2">
                                <i class="fas fa-shopping-cart text-oren"></i>
                                <span>📍 Bisa kamu cari di: <strong>{{ $rec['tempat'] }}</strong></span>
                            </div>
                            @endif
                            
                            @if(isset($rec['is_external']) && $rec['is_external'])
                            <div class="mt-3 text-xs text-blue-600 bg-blue-50 rounded-lg p-2 flex items-start gap-2">
                                <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                                <span>💡 Kamu juga bisa usulin buku ini ke petugas perpustakaan ya! Nanti kita bakal pertimbangkan buat dibeliin. Suaramu penting banget, Sobat Literasi! 📚✨</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- SARAN DARI TAMBANG ILMU --}}
        @if(isset($ai_results['suggestion']))
        <div class="mt-6 p-5 bg-gradient-to-r from-biru-50 to-indigo-50 rounded-2xl border border-biru/20">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-biru flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <div>
                    <h4 class="font-bold text-biru mb-1">💡 Saran dari TAMBANG ILMU</h4>
                    <p class="text-hitam/80">{{ $ai_results['suggestion'] }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- KATA KUNCI ALTERNATIF --}}
        @if(isset($ai_results['alternative_keywords']) && count($ai_results['alternative_keywords']) > 0)
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-hitam/70">🔑 Coba juga cari dengan kata kunci:</span>
            <div class="flex flex-wrap gap-2">
                @foreach($ai_results['alternative_keywords'] as $keyword)
                <a href="{{ route('opac.ai.search') }}?q={{ urlencode($keyword) }}" 
                class="px-3 py-1.5 bg-white border border-gray-200 rounded-full text-sm text-hitam/70 hover:bg-oren hover:text-white hover:border-oren transition-all duration-200">
                    {{ $keyword }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- ==================== PENCARIAN BIASA ==================== --}}
    @if(!isset($ai_results) && $buku->count() > 0)
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-hitam">
                Hasil Pencarian
                @if(request('q'))
                    <span class="text-oren">"{{ request('q') }}"</span>
                @endif
            </h2>
            <p class="text-sm text-hitam/60 mt-1">
                Menampilkan {{ $buku->firstItem() ?? 0 }}-{{ $buku->lastItem() ?? 0 }} dari {{ $buku->total() }} koleksi
            </p>
        </div>
        
        <div class="flex gap-2">
            <select class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-oren bg-white" onchange="location = this.value">
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>
                    📅 Terbaru
                </option>
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'populer']) }}" {{ request('sort') == 'populer' ? 'selected' : '' }}>
                    🔥 Populer
                </option>
                <option value="{{ request()->fullUrlWithQuery(['sort' => 'judul']) }}" {{ request('sort') == 'judul' ? 'selected' : '' }}>
                    🔤 Judul A-Z
                </option>
            </select>
        </div>
    </div>
    @endif

    {{-- Grid Buku Pencarian Biasa --}}
    @if(!isset($ai_results) && $buku->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($buku as $item)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 group hover:-translate-y-2">
                <div class="relative h-52 bg-gradient-to-br from-biru-50 to-oren-50">
                    @php
                        $coverFound = false;
                    @endphp
                    
                    @if($item->sampul && Storage::disk('public')->exists($item->sampul))
                        <img src="{{ asset('storage/' . $item->sampul) }}" 
                             alt="{{ $item->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @php $coverFound = true; @endphp
                    @elseif($item->cover_path && Storage::disk('public')->exists($item->cover_path))
                        <img src="{{ asset('storage/' . $item->cover_path) }}" 
                             alt="{{ $item->judul }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @php $coverFound = true; @endphp
                    @endif
                    
                    @if(!$coverFound)
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-book-open text-5xl text-biru/30"></i>
                        </div>
                    @endif
                    
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 bg-white/95 backdrop-blur-sm rounded-lg text-xs font-semibold shadow-sm
                            {{ $item->tipe == 'digital' ? 'text-purple-600' : 'text-biru' }}">
                            {{ $item->tipe == 'digital' ? '📱 E-Book' : '📖 Buku Fisik' }}
                        </span>
                    </div>
                    
                    <div class="absolute bottom-3 left-3">
                        @if($item->tipe == 'digital')
                            @php $lisensiTersedia = $item->jumlah_lisensi - $item->lisensi_dipinjam; @endphp
                            @if($item->bisa_langsung_download ?? false)
                                <span class="px-2 py-1 bg-biru text-white text-xs rounded-full shadow-md flex items-center gap-1">
                                    ⬇️ Download Bebas
                                </span>
                            @elseif($lisensiTersedia > 0)
                                <span class="px-2 py-1 bg-hijau text-white text-xs rounded-full shadow-md">Tersedia ({{ $lisensiTersedia }})</span>
                            @else
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full shadow-md">Sedang Dipinjam</span>
                            @endif
                        @else
                            @if($item->stok_tersedia > 0)
                                <span class="px-2 py-1 bg-hijau text-white text-xs rounded-full shadow-md">Tersedia {{ $item->stok_tersedia }}</span>
                            @else
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full shadow-md">Dipinjam</span>
                            @endif
                        @endif
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="font-bold text-hitam mb-1 line-clamp-2">{{ $item->judul }}</h3>
                    <p class="text-sm text-hitam/60 mb-2 line-clamp-1">{{ $item->pengarang ?? 'Tanpa Pengarang' }}</p>
                    
                    <div class="flex items-center gap-2 text-xs text-hitam/50 mb-3">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-building"></i>
                            {{ Str::limit($item->penerbit ?? '-', 20) }}
                        </span>
                        <span>•</span>
                        <span>{{ $item->tahun_terbit ?? '-' }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <span class="inline-block px-2 py-1 bg-biru/10 text-biru rounded-full text-xs font-medium">
                            <i class="fas fa-tag mr-1"></i> {{ $item->kategori->nama ?? 'Umum' }}
                        </span>
                    </div>
                    
                    <a href="{{ route('opac.show', $item->id) }}" 
                       class="block text-center text-biru hover:text-oren text-sm font-semibold pt-2 border-t border-gray-100 flex items-center justify-center gap-1 transition-colors">
                        Lihat Detail <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $buku->withQueryString()->links() }}
        </div>
    @elseif(!isset($ai_results) && $buku->count() == 0 && !request('q'))
        {{-- Empty State --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-20 text-center">
            <div class="animate-float">
                <i class="fas fa-search text-7xl text-biru/20 mb-6"></i>
            </div>
            <h3 class="text-2xl font-bold text-hitam mb-3">Mulai Mencari Koleksi</h3>
            <p class="text-hitam/60 max-w-md mx-auto">Gunakan kotak pencarian di atas atau aktifkan <span class="text-oren font-semibold">Mode AI Cerdas</span> untuk pengalaman pencarian yang lebih pintar!</p>
            <div class="mt-6 flex flex-wrap gap-3 justify-center">
                <span class="px-3 py-1.5 bg-biru/10 text-biru rounded-full text-sm">📖 Novel</span>
                <span class="px-3 py-1.5 bg-biru/10 text-biru rounded-full text-sm">📚 Pelajaran</span>
                <span class="px-3 py-1.5 bg-biru/10 text-biru rounded-full text-sm">💻 Pemrograman</span>
                <span class="px-3 py-1.5 bg-biru/10 text-biru rounded-full text-sm">🎓 Motivasi</span>
            </div>
        </div>
    @elseif(!isset($ai_results) && $buku->count() == 0 && request('q'))
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-20 text-center">
            <i class="fas fa-frown-open text-7xl text-gray-300 mb-6"></i>
            <h3 class="text-xl font-bold text-hitam mb-2">Tidak Ditemukan</h3>
            <p class="text-hitam/60 mb-6 max-w-md mx-auto">Maaf, koleksi yang Anda cari <strong>"{{ request('q') }}"</strong> tidak ditemukan.</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('opac.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-biru text-white rounded-xl hover:bg-biru-dark transition">
                    <i class="fas fa-sync-alt"></i> Reset Pencarian
                </a>
                <button onclick="document.getElementById('useAI').click()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-oren text-white rounded-xl hover:bg-oren-dark transition">
                    <i class="fas fa-robot"></i> Coba dengan AI
                </button>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleAISearch() {
    const useAI = document.getElementById('useAI').checked;
    const searchInput = document.getElementById('searchInput');
    const currentUrl = new URL(window.location.href);
    
    if (useAI) {
        const query = searchInput.value;
        if (query && query.trim() !== '') {
            window.location.href = '{{ route("opac.ai.search") }}?q=' + encodeURIComponent(query);
        } else {
            currentUrl.searchParams.set('use_ai', '1');
            window.location.href = currentUrl.toString();
        }
    } else {
        currentUrl.searchParams.delete('use_ai');
        if (!searchInput.value) {
            currentUrl.searchParams.delete('q');
        }
        window.location.href = currentUrl.toString();
    }
}

// Focus search input on load
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});

// Submit form on Enter key
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('searchForm').submit();
    }
});

@if(isset($ai_results) && ($is_ai_search ?? false))
    console.log('AI Search Results:', @json($ai_results));
    
    @if(!($ai_results['success'] ?? true) && isset($ai_results['error']))
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-5 right-5 z-50 bg-oren text-white px-5 py-3 rounded-xl shadow-lg animate-slide-in flex items-center gap-2';
        toast.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + @json($ai_results['error']);
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    @endif
@endif
</script>
@endpush

@push('styles')
<style>
.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(100px); }
    to { opacity: 1; transform: translateX(0); }
}

.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #3B82F6;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #2563EB;
}
</style>
@endpush
@endsection