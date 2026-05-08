@extends('opac.layouts.app')

@section('title', $buku->judul . ' - Detail Buku')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex text-sm text-gray-500">
            <a href="{{ route('opac.index') }}" class="hover:text-indigo-600">OPAC</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $buku->judul }}</span>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column - Cover & Info --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-20">
                {{-- Cover --}}
                <div class="aspect-[2/3] bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg overflow-hidden mb-4">
                    @php
                        $coverFound = false;
                    @endphp
                    
                    {{-- Cek sampul --}}
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
                    
                    {{-- Placeholder --}}
                    @if(!$coverFound)
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Rating Section --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <x-star-rating :rating="$buku->average_rating" 
                                          size="lg" 
                                          :showCount="true" 
                                          :count="$buku->total_ratings" />
                            @if($buku->total_ratings > 0)
                                <p class="text-xs text-gray-500 mt-1">
                                    Dari {{ $buku->total_ratings }} ulasan
                                </p>
                            @endif
                        </div>
                        
                        @auth
                            @if(Auth::user()->role === 'anggota')
                                <button type="button" 
                                        onclick="showRatingModal()"
                                        class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Beri Ulasan
                                </button>
                            @endif
                        @endauth
                    </div>
                    
                    {{-- Display User's Review --}}
                    @auth
                        @php
                            $userReview = $buku->ulasan()->where('user_id', Auth::id())->first();
                        @endphp
                        @if($userReview)
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <x-star-rating :rating="$userReview->rating" size="sm" />
                                            <span class="text-xs text-gray-500">
                                                {{ $userReview->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        @if(!$userReview->is_approved)
                                            <p class="text-xs text-yellow-600 mt-1">
                                                ⏳ Menunggu verifikasi
                                            </p>
                                        @endif
                                    </div>
                                    <button type="button" 
                                            onclick="showRatingModal()"
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        Edit
                                    </button>
                                </div>
                                @if($userReview->ulasan)
                                    <p class="text-sm text-gray-700 mt-2">{{ $userReview->ulasan }}</p>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
                
                {{-- Info Box --}}
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Tipe</span>
                        <span class="font-medium {{ $buku->tipe == 'digital' ? 'text-purple-600' : 'text-blue-600' }}">
                            {{ $buku->tipe == 'digital' ? '💻 E-Book' : '📖 Buku Fisik' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Ketersediaan</span>
                        @if($buku->tipe == 'digital')
                            @php $tersedia = $buku->jumlah_lisensi - $buku->lisensi_dipinjam; @endphp
                            @if($buku->bisa_langsung_download ?? false)
                                <span class="text-blue-600 font-medium">⬇️ Download Bebas</span>
                            @elseif($tersedia > 0)
                                <span class="text-green-600 font-medium">Tersedia ({{ $tersedia }} lisensi)</span>
                            @else
                                <span class="text-red-600 font-medium">Sedang Dipinjam</span>
                            @endif
                        @else
                            @if($buku->stok_tersedia > 0)
                                <span class="text-green-600 font-medium">Tersedia {{ $buku->stok_tersedia }}</span>
                            @else
                                <span class="text-red-600 font-medium">Dipinjam</span>
                            @endif
                        @endif
                    </div>
                    
                    @if($buku->tipe == 'fisik')
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Lokasi Rak</span>
                        <span class="font-mono text-sm">{{ $buku->rak ?? '-' }}{{ $buku->baris ? ' / ' . $buku->baris : '' }}</span>
                    </div>
                    @endif
                </div>
                
                {{-- Action Buttons --}}
                @if($buku->bisa_langsung_download)
                    {{-- Download Bebas - Bisa diakses TANPA LOGIN --}}
                    <a href="{{ route('opac.download', $buku->id) }}" 
                    class="w-full mt-4 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl flex items-center justify-center gap-2 transition-all font-medium shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        ⬇️ Download Bebas
                    </a>
                @elseif($buku->tipe == 'fisik')
                    @auth
                        @if(Auth::user()->isAnggota())
                            {{-- Tombol Booking untuk Anggota --}}
                            <a href="{{ route('anggota.booking.create', $buku->id) }}" 
                            class="w-full mt-4 px-4 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-xl flex items-center justify-center gap-2 transition-all font-medium shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                📅 Booking Buku
                            </a>
                        @endif
                        
                        {{-- Informasi untuk anggota --}}
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg text-center">
                            <p class="text-xs text-blue-700">
                                💡 Ingin meminjam langsung? <br>
                                Kunjungi perpustakaan dan hubungi petugas.
                            </p>
                        </div>
                    @else
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg text-center">
                            <p class="text-sm text-yellow-800 mb-2">Ingin meminjam atau booking buku?</p>
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Login</a>
                            <span class="text-gray-500"> atau </span>
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Daftar</a>
                        </div>
                    @endauth
                @elseif($buku->tipe == 'digital')
                    {{-- E-Book --}}
                    @auth
                        @if(Auth::user()->isAnggota())
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg text-center">
                                <p class="text-sm text-blue-700">
                                    📚 Buku digital dapat diakses setelah login.<br>
                                    Kunjungi menu "Koleksi Digital" di dashboard anggota.
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg text-center">
                            <p class="text-sm text-yellow-800 mb-2">Login untuk mengakses e-book</p>
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Login</a>
                            <span class="text-gray-500"> atau </span>
                            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Daftar</a>
                        </div>
                    @endauth
                @endif
            </div>
        </div>

        {{-- Right Column - Detail Informasi --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                
                {{-- Judul --}}
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $buku->judul }}</h1>
                
                {{-- Pengarang --}}
                @if($buku->pengarang)
                <p class="text-lg text-gray-600 mb-4">{{ $buku->pengarang }}</p>
                @endif
                
                {{-- Meta Info --}}
                <div class="flex flex-wrap gap-4 mb-6 text-sm">
                    @if($buku->penerbit)
                    <span class="flex items-center gap-1 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                        </svg>
                        {{ $buku->penerbit }}
                    </span>
                    @endif
                    
                    @if($buku->tahun_terbit)
                    <span class="flex items-center gap-1 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $buku->tahun_terbit }}
                    </span>
                    @endif
                    
                    @if($buku->isbn)
                    <span class="flex items-center gap-1 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                        </svg>
                        ISBN: {{ $buku->isbn }}
                    </span>
                    @endif
                </div>

                {{-- Deskripsi --}}
                @if($buku->deskripsi)
                <div class="mb-6">
                    <h2 class="font-semibold text-gray-800 mb-3">Deskripsi</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $buku->deskripsi }}</p>
                </div>
                @endif

                {{-- Detail Fisik --}}
                @if($buku->tipe == 'fisik')
                <div class="border-t pt-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Detail Fisik</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
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
                        
                        @if($buku->edisi)
                        <div>
                            <p class="text-xs text-gray-500">Edisi</p>
                            <p class="font-medium">{{ $buku->edisi }}</p>
                        </div>
                        @endif
                        
                        @if($buku->format)
                        <div>
                            <p class="text-xs text-gray-500">Format</p>
                            <p class="font-medium">{{ $buku->format }}</p>
                        </div>
                        @endif
                        
                        @if($buku->ukuran)
                        <div>
                            <p class="text-xs text-gray-500">Ukuran</p>
                            <p class="font-medium">{{ $buku->ukuran }}</p>
                        </div>
                        @endif
                        
                        @if($buku->berat)
                        <div>
                            <p class="text-xs text-gray-500">Berat</p>
                            <p class="font-medium">{{ $buku->berat }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Detail Digital --}}
                @if($buku->tipe == 'digital')
                <div class="border-t pt-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Detail Digital</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($buku->format)
                        <div>
                            <p class="text-xs text-gray-500">Format File</p>
                            <p class="font-medium">{{ strtoupper($buku->format) }}</p>
                        </div>
                        @endif
                        
                        @if($buku->file_size)
                        <div>
                            <p class="text-xs text-gray-500">Ukuran File</p>
                            <p class="font-medium">{{ $buku->formatted_file_size }}</p>
                        </div>
                        @endif
                        
                        @if($buku->akses_digital)
                        <div>
                            <p class="text-xs text-gray-500">Tipe Akses</p>
                            <p class="font-medium capitalize">{{ str_replace('_', ' ', $buku->akses_digital) }}</p>
                        </div>
                        @endif
                        
                        @if($buku->durasi_pinjam_hari)
                        <div>
                            <p class="text-xs text-gray-500">Durasi Pinjam</p>
                            <p class="font-medium">{{ $buku->durasi_pinjam_hari }} hari</p>
                        </div>
                        @endif
                        
                        @if($buku->penerbit_lisensi)
                        <div>
                            <p class="text-xs text-gray-500">Penerbit Lisensi</p>
                            <p class="font-medium">{{ $buku->penerbit_lisensi }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Reviews Section --}}
                <div class="border-t pt-6 mt-6">
                    <h2 class="font-semibold text-gray-800 mb-4">
                        Ulasan Pembaca 
                        @if($buku->total_ratings > 0)
                            <span class="text-sm text-gray-500 font-normal">({{ $buku->total_ratings }} ulasan)</span>
                        @endif
                    </h2>
                    
                    @if($buku->total_ratings > 0)
                        {{-- Rating Distribution --}}
                        <div class="mb-6">
                            @for($i = 5; $i >= 1; $i--)
                                @php
                                    $count = $buku->rating_distribution[$i] ?? 0;
                                    $percentage = $buku->total_ratings > 0 ? ($count / $buku->total_ratings) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-16 text-sm text-gray-600">{{ $i }} bintang</div>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-yellow-400 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="w-12 text-sm text-gray-500">{{ $count }}</div>
                                </div>
                            @endfor
                        </div>
                        
                        {{-- Reviews List --}}
                        <div class="space-y-4">
                            @foreach($buku->ulasanDisetujui()->with('user')->latest()->take(10)->get() as $review)
                                <div class="border-b border-gray-100 pb-4 last:border-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <x-star-rating :rating="$review->rating" size="sm" />
                                            <p class="text-sm font-medium text-gray-800 mt-1">{{ $review->user->name }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->ulasan)
                                        <p class="text-sm text-gray-600">{{ $review->ulasan }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <p>Belum ada ulasan untuk buku ini.</p>
                            @auth
                                @if(Auth::user()->role === 'anggota')
                                    <p class="text-sm mt-2">Jadilah yang pertama memberikan ulasan!</p>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>

                {{-- Other Collections --}}
                @if($buku->kategori && $buku->kategori->buku->where('id', '!=', $buku->id)->count() > 0)
                <div class="border-t pt-6 mt-6">
                    <h2 class="font-semibold text-gray-800 mb-4">Koleksi Lainnya di Kategori {{ $buku->kategori->nama }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($buku->kategori->buku->where('id', '!=', $buku->id)->take(3) as $bukuLain)
                        <a href="{{ route('opac.show', $bukuLain->id) }}" class="block group">
                            <div class="bg-gray-50 rounded-lg p-3 hover:bg-indigo-50 transition-colors">
                                <p class="font-medium text-sm group-hover:text-indigo-600 line-clamp-2">{{ $bukuLain->judul }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $bukuLain->pengarang ?? '-' }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Rating Modal --}}
<div id="ratingModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeRatingModal()"></div>
        
        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Ulasan Buku: {{ $buku->judul }}
            </h3>
            
            <form id="ratingForm" onsubmit="submitRating(event)">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rating Anda
                    </label>
                    <div class="flex gap-1 text-2xl" id="starContainer">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star cursor-pointer text-gray-400 transition-colors" data-rating="{{ $i }}"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingValue" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ulasan (Opsional)
                    </label>
                    <textarea name="ulasan" id="ulasanValue" rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Ceritakan pengalaman Anda membaca buku ini..."></textarea>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" 
                            onclick="closeRatingModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                            id="submitRatingBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedRating = 0;
let existingReview = null;

function showRatingModal() {
    // Cek apakah user sudah login sebagai anggota
    @auth
        @if(Auth::user()->role === 'anggota')
            document.getElementById('ratingModal').classList.remove('hidden');
            loadExistingRating();
        @else
            alert('Silakan login sebagai anggota untuk memberikan rating.');
            window.location.href = '{{ route("login") }}';
        @endif
    @else
        alert('Silakan login terlebih dahulu untuk memberikan rating.');
        window.location.href = '{{ route("login") }}';
    @endauth
}

function closeRatingModal() {
    document.getElementById('ratingModal').classList.add('hidden');
    resetRatingForm();
}

function resetRatingForm() {
    selectedRating = 0;
    document.getElementById('ratingValue').value = '';
    document.getElementById('ulasanValue').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('submitRatingBtn').innerHTML = 'Kirim Ulasan';
    
    // Reset stars
    const stars = document.querySelectorAll('#starContainer i');
    stars.forEach(star => {
        star.classList.remove('fas');
        star.classList.add('far');
        star.classList.remove('text-yellow-400');
        star.classList.add('text-gray-400');
    });
}

function loadExistingRating() {
    @auth
        @if(Auth::user()->role === 'anggota')
            fetch('{{ route("anggota.rating.get", $buku->id) }}')
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.data.user_review) {
                        existingReview = result.data.user_review;
                        selectedRating = existingReview.rating;
                        document.getElementById('ratingValue').value = selectedRating;
                        document.getElementById('ulasanValue').value = existingReview.ulasan || '';
                        document.getElementById('formMethod').value = 'PUT';
                        document.getElementById('submitRatingBtn').innerHTML = 'Update Ulasan';
                        updateStarDisplay(selectedRating);
                    }
                })
                .catch(error => {
                    console.error('Error loading rating:', error);
                });
        @endif
    @endauth
}

function updateStarDisplay(rating) {
    const stars = document.querySelectorAll('#starContainer i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far', 'text-gray-400');
            star.classList.add('fas', 'text-yellow-400');
        } else {
            star.classList.remove('fas', 'text-yellow-400');
            star.classList.add('far', 'text-gray-400');
        }
    });
}

// Setup star rating
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('starContainer');
    if (!container) return;
    
    const stars = container.querySelectorAll('i');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            document.getElementById('ratingValue').value = selectedRating;
            updateStarDisplay(selectedRating);
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            stars.forEach((s, idx) => {
                if (idx < rating) {
                    s.classList.remove('far', 'text-gray-400');
                    s.classList.add('fas', 'text-yellow-400');
                } else {
                    s.classList.remove('fas', 'text-yellow-400');
                    s.classList.add('far', 'text-gray-400');
                }
            });
        });
        
        container.addEventListener('mouseleave', function() {
            updateStarDisplay(selectedRating);
        });
    });
});

function submitRating(event) {
    event.preventDefault();
    
    @auth
        @if(Auth::user()->role === 'anggota')
            if (selectedRating === 0) {
                alert('Silakan pilih rating terlebih dahulu');
                return;
            }
            
            const form = document.getElementById('ratingForm');
            const formData = new FormData(form);
            const method = document.getElementById('formMethod').value;
            
            let url = '{{ route("anggota.rating.store", $buku->id) }}';
            let fetchOptions = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            };
            
            if (method === 'PUT') {
                fetchOptions.method = 'POST';
                formData.append('_method', 'PUT');
            }
            
            fetch(url, fetchOptions)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert(result.message);
                        closeRatingModal();
                        location.reload();
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        @else
            alert('Hanya anggota yang dapat memberikan rating.');
            window.location.href = '{{ route("login") }}';
        @endif
    @else
        alert('Silakan login terlebih dahulu untuk memberikan rating.');
        window.location.href = '{{ route("login") }}';
    @endauth
}
</script>
@endpush