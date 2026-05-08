{{-- resources/views/auth/check-verification.blade.php --}}
@extends('auth.layouts.guest')

@section('title', 'Cek Status Verifikasi - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Cek Status Pendaftaran 🔍')
@section('auth-subtitle', 'Masukkan email Anda untuk mengetahui status verifikasi')

@section('content')
<form method="POST" action="{{ route('verification.check.submit') }}" class="space-y-6">
    @csrf

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Info -->
    <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Cek Status Pendaftaran</p>
            <p class="text-xs text-gray-500">Masukkan email yang digunakan saat mendaftar</p>
        </div>
    </div>

    <!-- Email -->
    <div class="group">
        <label for="email" class="form-label">
            <i class="fas fa-envelope text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
            Email Pendaftar
        </label>
        <div class="relative">
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autofocus
                   class="form-input pl-12"
                   placeholder="Masukkan email Anda">
            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-orange-500 transition-colors"></i>
        </div>
    </div>

    <!-- Tombol Cek -->
    <button type="submit" class="btn-primary flex items-center justify-center gap-3 w-full">
        <i class="fas fa-search"></i>
        <span>Cek Status Verifikasi</span>
        <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
    </button>

    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">atau</span>
        </div>
    </div>

    <a href="{{ route('login') }}" 
       class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
        <i class="fas fa-sign-in-alt"></i>
        <span>Kembali ke Halaman Login</span>
    </a>
</form>
@endsection