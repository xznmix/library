@extends('petugas.layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto">
    
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('petugas.booking.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">📋 Detail Booking</h1>
    </div>

    {{-- Informasi Booking --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Kode Booking</p>
                <p class="text-xl font-mono font-bold">{{ $booking->kode_booking }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                @php
                    $statusColor = [
                        'menunggu' => 'yellow',
                        'disetujui' => 'green',
                        'ditolak' => 'red',
                        'diambil' => 'blue',
                        'hangus' => 'gray',
                    ][$booking->status] ?? 'gray';
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Booking</p>
                <p class="font-medium">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Batas Ambil</p>
                <p class="font-medium {{ now()->greaterThan($booking->batas_ambil) && $booking->status == 'disetujui' ? 'text-red-600' : '' }}">
                    {{ \Carbon\Carbon::parse($booking->batas_ambil)->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Informasi Anggota --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">👤 Informasi Anggota</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Nama</p>
                <p class="font-medium">{{ $booking->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $booking->user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">No. Telepon</p>
                <p class="font-medium">{{ $booking->user->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Role</p>
                <p class="font-medium">{{ ucfirst($booking->user->role) }}</p>
            </div>
        </div>
    </div>

    {{-- Informasi Buku --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">📖 Informasi Buku</h2>
        <div class="flex gap-4">
            <div class="w-24 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                @if($booking->buku->sampul && Storage::disk('public')->exists($booking->buku->sampul))
                    <img src="{{ asset('storage/' . $booking->buku->sampul) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 text-lg">{{ $booking->buku->judul }}</h3>
                <p class="text-gray-600">{{ $booking->buku->pengarang ?? 'Tanpa Pengarang' }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ $booking->buku->penerbit }} ({{ $booking->buku->tahun_terbit ?? '-' }})</p>
                <p class="text-xs text-gray-500 mt-2">📍 Rak: {{ $booking->buku->rak ?? 'Rak Umum' }}</p>
                <p class="text-xs text-gray-500">📊 Stok Tersedia: {{ $booking->buku->stok_siap_pinjam }}</p>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="font-semibold text-gray-800 mb-4">⚡ Aksi</h2>
        
        @if($booking->status == 'menunggu')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <form action="{{ route('petugas.booking.approve', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all">
                        ✅ Setujui Booking
                    </button>
                </form>
                
                <button type="button" onclick="showRejectModal()" 
                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all">
                    ❌ Tolak Booking
                </button>
            </div>
        @elseif($booking->status == 'disetujui')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <form action="{{ route('petugas.booking.process-pickup', $booking->id) }}" method="POST" id="pickupForm">
                    @csrf
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div>
                            <label class="text-xs text-gray-500">Tgl Pinjam</label>
                            <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-2 py-1 text-sm border rounded" required>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">Jatuh Tempo</label>
                            <input type="date" name="tgl_jatuh_tempo" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                   class="w-full px-2 py-1 text-sm border rounded" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all">
                        📖 Proses Ambil Buku
                    </button>
                </form>
                
                <form action="{{ route('petugas.booking.expire', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-all"
                            onclick="return confirm('Yakin ingin menghanguskan booking ini? Buku akan dikembalikan ke stok.')">
                        ⏰ Hanguskan Booking
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

{{-- Modal Tolak Booking --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">❌ Tolak Booking</h3>
            <form action="{{ route('petugas.booking.reject', $booking->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan</label>
                    <textarea name="alasan" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200" 
                              placeholder="Contoh: Stok buku sedang habis..." required></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Ya, Tolak
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRejectModal();
    }
});
</script>
@endpush
@endsection