@extends('anggota.layouts.app')

@section('title', 'Booking Buku')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('opac.show', $buku->id) }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">📅 Booking Buku</h1>
    </div>

    {{-- Informasi Buku --}}
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex gap-4">
            {{-- Cover Mini --}}
            <div class="w-24 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                    <img src="{{ asset('storage/' . $buku->sampul) }}" alt="{{ $buku->judul }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                @endif
            </div>
            
            {{-- Info Buku --}}
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $buku->judul }}</h2>
                <p class="text-gray-600 text-sm mb-2">{{ $buku->pengarang ?? 'Tanpa Pengarang' }}</p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $buku->penerbit ?? '-' }}</span>
                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $buku->tahun_terbit ?? '-' }}</span>
                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded">📍 {{ $buku->rak ?? 'Rak Umum' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Booking --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="mb-6">
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">📌 Aturan Booking:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Maksimal 2 booking aktif per anggota</li>
                            <li>Booking harus diambil maksimal 2 hari setelah tanggal ambil</li>
                            <li>Jika tidak diambil dalam batas waktu, booking akan hangus</li>
                            <li>Setelah booking disetujui, buku akan direservasi untuk Anda</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('anggota.booking.store', $buku->id) }}" method="POST">
            @csrf
            
            {{-- Pilih Tanggal Ambil --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📅 Tanggal Ambil Buku <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($tanggalOptions as $option)
                        <label class="relative flex items-center p-4 border rounded-lg cursor-pointer hover:bg-indigo-50 transition-all {{ old('tanggal_ambil') == $option['value'] ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            <input type="radio" 
                                   name="tanggal_ambil" 
                                   value="{{ $option['value'] }}"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   {{ old('tanggal_ambil') == $option['value'] ? 'checked' : '' }}
                                   required>
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-800">
                                    🗓️ {{ $option['hari'] }}
                                </span>
                                <span class="block text-xs text-gray-500">
                                    {{ $option['label'] }}
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('tanggal_ambil')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">
                    ⚠️ Pilih tanggal saat Anda bisa datang ke perpustakaan. Booking akan hangus jika tidak diambil dalam 2 hari.
                </p>
            </div>

            {{-- Informasi Tambahan --}}
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p>Setelah booking disetujui petugas, Anda akan mendapat notifikasi. Silakan datang ke perpustakaan pada tanggal yang dipilih.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex gap-3">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-md hover:shadow-lg">
                    ✅ Konfirmasi Booking
                </button>
                <a href="{{ route('opac.show', $buku->id) }}" 
                   class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    input[type="radio"]:checked + div {
        border-color: #4f46e5;
    }
</style>
@endpush