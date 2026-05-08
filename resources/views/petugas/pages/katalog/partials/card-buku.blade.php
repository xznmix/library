@props(['buku'])

<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
    
    {{-- Cover Buku --}}
    <div class="relative h-48 bg-gradient-to-br from-indigo-50 to-purple-50 overflow-hidden">
        @php
            $coverFound = false;
        @endphp
        
        {{-- Cek sampul dulu --}}
        @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
            <img src="{{ asset('storage/' . $buku->sampul) }}" 
                 alt="{{ $buku->judul }}"
                 class="w-full h-full object-cover">
            @php $coverFound = true; @endphp
        {{-- Cek cover_path --}}
        @elseif($buku->cover_path && Storage::disk('public')->exists($buku->cover_path))
            <img src="{{ asset('storage/' . $buku->cover_path) }}" 
                 alt="{{ $buku->judul }}"
                 class="w-full h-full object-cover">
            @php $coverFound = true; @endphp
        @endif
        
        {{-- Placeholder jika tidak ada cover --}}
        @if(!$coverFound)
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        @endif
        
        {{-- Badge Tipe --}}
        <div class="absolute top-2 right-2">
            <span class="px-2 py-1 text-xs font-semibold rounded-full shadow-sm
                {{ $buku->tipe == 'fisik' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                {{ $buku->tipe == 'fisik' ? '📖 Fisik' : '💻 Digital' }}
            </span>
        </div>
        
        {{-- Badge Status Stok --}}
        @if($buku->tipe == 'fisik')
            @if($buku->stok_tersedia > 0)
                <div class="absolute bottom-2 left-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 shadow-sm">
                        ✓ Tersedia {{ $buku->stok_tersedia }}
                    </span>
                </div>
            @else
                <div class="absolute bottom-2 left-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 shadow-sm">
                        ✗ Dipinjam
                    </span>
                </div>
            @endif
        @else
            <div class="absolute bottom-2 left-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 shadow-sm">
                    ✓ Selalu Tersedia
                </span>
            </div>
        @endif
    </div>
    
    {{-- Info Buku --}}
    <div class="p-4">
        <h3 class="font-bold text-gray-800 mb-1 line-clamp-2" title="{{ $buku->judul }}">
            {{ $buku->judul }}
        </h3>
        
        <p class="text-sm text-gray-600 mb-2 line-clamp-1">
            {{ $buku->pengarang ?? 'Tanpa Pengarang' }}
        </p>
        
        {{-- Meta Info --}}
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
            <span class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                </svg>
                {{ $buku->penerbit ?? '-' }}
            </span>
            <span>•</span>
            <span>{{ $buku->tahun_terbit ?? '-' }}</span>
        </div>
        
        {{-- Kategori --}}
        <div class="mb-3">
            <span class="inline-block px-2 py-1 text-xs bg-indigo-50 text-indigo-700 rounded-full">
                {{ $buku->kategori->nama ?? 'Tanpa Kategori' }}
            </span>
        </div>
        
        {{-- Quick Actions --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <button onclick="showDetail({{ $buku->id }})" 
                    class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Detail
            </button>
            
            <div class="flex gap-2">
                <a href="{{ route('petugas.buku.edit', $buku->id) }}" 
                   class="p-1 text-gray-500 hover:text-blue-600 transition-colors"
                   title="Edit Buku">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                
                @if($buku->tipe == 'digital' || $buku->stok_tersedia > 0)
                <a href="{{ route('petugas.sirkulasi.peminjaman.create') }}" 
                   class="p-1 text-gray-500 hover:text-green-600 transition-colors"
                   title="Pinjam Buku">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>