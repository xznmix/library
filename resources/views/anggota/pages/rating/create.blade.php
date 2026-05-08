@extends('anggota.layouts.app')

@section('title', 'Beri Rating untuk Buku')
@section('page-title', 'Beri Rating')

@section('content')
<div class="max-w-2xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('anggota.riwayat.index') }}" class="text-biru hover:text-biru-dark flex items-center gap-1 text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Riwayat
        </a>
        
        <h1 class="text-2xl font-bold text-gray-800">⭐ Beri Rating & Ulasan</h1>
        <p class="text-gray-500 mt-1">Bagikan pengalaman Anda membaca buku ini</p>
    </div>

    @if($sudahRating ?? false)
        <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>Anda sudah memberikan rating untuk buku ini. Anda dapat mengedit ulasan di bawah ini.</span>
            </div>
        </div>
    @endif

    {{-- Informasi Buku --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex gap-4">
            <div class="w-24 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                @if($peminjaman->buku->sampul && Storage::disk('public')->exists($peminjaman->buku->sampul))
                    <img src="{{ asset('storage/' . $peminjaman->buku->sampul) }}" 
                         alt="{{ $peminjaman->buku->judul }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $peminjaman->buku->judul }}</h2>
                <p class="text-gray-600 text-sm">{{ $peminjaman->buku->pengarang ?? 'Tanpa Pengarang' }}</p>
                <p class="text-gray-500 text-xs mt-2">
                    Dipinjam: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') }} |
                    Dikembalikan: {{ \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form Rating --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form id="ratingForm">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Rating Anda <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-2 text-4xl" id="starContainer">
                    <i class="far fa-star cursor-pointer transition-all hover:scale-110 hover:text-yellow-400" data-rating="1"></i>
                    <i class="far fa-star cursor-pointer transition-all hover:scale-110 hover:text-yellow-400" data-rating="2"></i>
                    <i class="far fa-star cursor-pointer transition-all hover:scale-110 hover:text-yellow-400" data-rating="3"></i>
                    <i class="far fa-star cursor-pointer transition-all hover:scale-110 hover:text-yellow-400" data-rating="4"></i>
                    <i class="far fa-star cursor-pointer transition-all hover:scale-110 hover:text-yellow-400" data-rating="5"></i>
                </div>
                <input type="hidden" name="rating" id="ratingValue" required>
                <p class="text-xs text-gray-500 mt-2" id="ratingHint"></p>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ulasan (Opsional)
                </label>
                <textarea name="ulasan" id="ulasanValue" rows="5"
                          class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-biru focus:border-biru transition p-3"
                          placeholder="Ceritakan pengalaman Anda membaca buku ini... Apa yang Anda suka? Apa yang menarik?"></textarea>
                <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" 
                        id="submitBtn"
                        class="flex-1 px-6 py-3 bg-biru hover:bg-biru-dark text-white rounded-lg font-medium transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    Kirim Rating & Ulasan
                </button>
                <a href="{{ route('anggota.riwayat.index') }}" 
                   class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all text-center">
                    Lewati
                </a>
            </div>
        </form>
        
        <div class="mt-6 p-4 bg-hijau-50 rounded-lg border border-hijau-100">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-hijau mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <div class="text-sm text-hijau">
                    <p class="font-medium">🎉 Dapatkan +5 Poin!</p>
                    <p>Dengan memberikan rating dan ulasan, Anda akan mendapatkan poin pembaca aktif.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let selectedRating = 0;
const ratingHint = {
    1: '😞 Buruk - Buku tidak sesuai harapan',
    2: '😐 Cukup - Biasa saja',
    3: '🙂 Baik - Cukup menarik',
    4: '😊 Bagus - Sangat menarik',
    5: '🤩 Luar Biasa - Wajib baca!'
};

// Setup star rating
const stars = document.querySelectorAll('#starContainer i');
const ratingValue = document.getElementById('ratingValue');
const ratingHintEl = document.getElementById('ratingHint');

stars.forEach(star => {
    star.addEventListener('click', function() {
        selectedRating = parseInt(this.getAttribute('data-rating'));
        ratingValue.value = selectedRating;
        updateStarDisplay(selectedRating);
        ratingHintEl.innerHTML = ratingHint[selectedRating] || '';
        ratingHintEl.className = 'text-xs mt-2 text-biru font-medium';
    });
    
    star.addEventListener('mouseenter', function() {
        const rating = parseInt(this.getAttribute('data-rating'));
        stars.forEach((s, idx) => {
            if (idx < rating) {
                s.classList.remove('far');
                s.classList.add('fas');
                s.classList.add('text-yellow-400');
            } else {
                s.classList.remove('fas');
                s.classList.add('far');
                s.classList.remove('text-yellow-400');
            }
        });
    });
});

document.getElementById('starContainer').addEventListener('mouseleave', function() {
    updateStarDisplay(selectedRating);
});

function updateStarDisplay(rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('far');
            star.classList.add('fas');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
            star.classList.remove('text-yellow-400');
        }
    });
}

// Form submission
document.getElementById('ratingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedRating === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Silakan pilih rating terlebih dahulu!',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Memproses...';
    
    fetch('{{ route("anggota.rating.store", $peminjaman->buku_id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            rating: selectedRating,
            ulasan: document.getElementById('ulasanValue').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Terima Kasih!',
                text: data.message,
                confirmButtonColor: '#3B82F6'
            }).then(() => {
                window.location.href = '{{ route("anggota.riwayat.index") }}';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message,
                confirmButtonColor: '#3B82F6'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Kirim Rating & Ulasan';
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan. Silakan coba lagi.',
            confirmButtonColor: '#3B82F6'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Kirim Rating & Ulasan';
    });
});
</script>

<style>
.fa-star, .far.fa-star {
    transition: all 0.2s ease;
}
</style>
@endsection