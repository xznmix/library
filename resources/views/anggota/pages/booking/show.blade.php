@extends('anggota.layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('anggota.booking.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">📋 Detail Booking</h1>
    </div>

    {{-- Status Booking --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-sm text-gray-500">Kode Booking</p>
                <p class="text-xl font-mono font-bold text-gray-800">{{ $booking->kode_booking }}</p>
            </div>
            <div>
                @php
                    $statusColor = [
                        'menunggu' => 'yellow',
                        'disetujui' => 'green',
                        'ditolak' => 'red',
                        'diambil' => 'blue',
                        'hangus' => 'gray',
                    ][$booking->status] ?? 'gray';
                    
                    $statusIcon = [
                        'menunggu' => '⏳',
                        'disetujui' => '✅',
                        'ditolak' => '❌',
                        'diambil' => '📖',
                        'hangus' => '⏰',
                    ][$booking->status] ?? '❓';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                    {{ $statusIcon }} {{ ucfirst($booking->status) }}
                </span>
            </div>
        </div>

        {{-- Informasi Buku --}}
        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-800 mb-3">Informasi Buku</h3>
            <div class="flex gap-4">
                <div class="w-20 h-28 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    @if($booking->buku->sampul && Storage::disk('public')->exists($booking->buku->sampul))
                        <img src="{{ asset('storage/' . $booking->buku->sampul) }}" alt="{{ $booking->buku->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800">{{ $booking->buku->judul }}</h4>
                    <p class="text-sm text-gray-600">{{ $booking->buku->pengarang ?? 'Tanpa Pengarang' }}</p>
                    <p class="text-xs text-gray-500 mt-1">📍 Rak: {{ $booking->buku->rak ?? 'Rak Umum' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline Booking --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-800 mb-4">⏱️ Timeline Booking</h3>
        
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Booking Dibuat</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if($booking->status == 'disetujui' || $booking->status == 'diambil')
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Booking Disetujui</p>
                    <p class="text-sm text-gray-500">Oleh: {{ $booking->petugas->name ?? 'Petugas' }}</p>
                </div>
            </div>
            @endif

            @if($booking->status == 'ditolak')
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Booking Ditolak</p>
                    <p class="text-sm text-gray-500">Alasan: {{ $booking->catatan_penolakan ?? '-' }}</p>
                </div>
            </div>
            @endif

            @if($booking->status == 'diambil')
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">Buku Diambil</p>
                    <p class="text-sm text-gray-500">Booking telah diproses menjadi peminjaman</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Informasi Penting --}}
        @if($booking->status == 'disetujui')
        <div class="mt-6 bg-yellow-50 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium text-yellow-800">⚠️ Segera Ambil Buku!</p>
                    <p class="text-sm text-yellow-700">
                        Booking ini harus diambil sebelum <strong>{{ \Carbon\Carbon::parse($booking->batas_ambil)->format('d/m/Y H:i') }}</strong>.
                        Jika tidak diambil, booking akan hangus otomatis.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($booking->status == 'menunggu')
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="font-medium text-blue-800">⏳ Menunggu Konfirmasi Petugas</p>
                    <p class="text-sm text-blue-700">
                        Booking Anda sedang menunggu konfirmasi dari petugas perpustakaan.
                        Silakan cek notifikasi untuk perkembangan selanjutnya.
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($booking->status == 'menunggu')
        <div class="mt-4">
            <form action="{{ route('anggota.booking.cancel', $booking->id) }}" method="POST" 
                  onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg font-medium transition-all">
                    ❌ Batalkan Booking
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection