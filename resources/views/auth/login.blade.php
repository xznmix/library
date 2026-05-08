@extends('auth.layouts.guest')

@section('title', 'Login - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Selamat Datang')
@section('auth-subtitle', 'Silakan masuk untuk melanjutkan')

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-6">
    @csrf

    @if (session('status'))
        <div class="bg-green-50 border-l-4 border-hijau text-green-700 px-5 py-4 rounded-lg text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-hijau text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-5 py-4 rounded-lg text-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-envelope text-gray-400 mr-2"></i> Email
        </label>
        <input id="email" 
               type="email" 
               name="email" 
               value="{{ old('email') }}" 
               required 
               autofocus
               class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-oren focus:ring-2 focus:ring-oren/20 transition-all text-base"
               placeholder="nama@email.com">
        @error('email')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-lock text-gray-400 mr-2"></i> Password
        </label>
        <div class="relative">
            <input id="password" 
                   type="password" 
                   name="password" 
                   required
                   class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-oren focus:ring-2 focus:ring-oren/20 transition-all text-base"
                   placeholder="Masukkan password">
            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-oren transition">
                <i id="passwordToggleIcon" class="fas fa-eye text-lg"></i>
            </button>
        </div>
        @error('password')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-oren focus:ring-oren">
            <span class="text-sm text-gray-600">Ingat saya</span>
        </label>
    </div>

    <!-- Tombol Login -->
    <button type="submit" class="w-full bg-biru hover:bg-biru-dark text-white font-semibold py-3 rounded-xl transition-all flex items-center justify-center gap-2 text-base">
        <i class="fas fa-sign-in-alt"></i>
        <span>Masuk</span>
    </button>

    <!-- Register Link -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">Belum punya akun?</span>
        </div>
    </div>

    <a href="{{ route('register') }}" 
       class="w-full block text-center border-2 border-oren text-oren hover:bg-oren hover:text-white font-semibold py-3 rounded-xl transition-all">
        <i class="fas fa-user-plus mr-2"></i>
        Daftar Anggota Baru
    </a>

    <!-- Cek Status -->
    <div class="text-center pt-2">
        <a href="{{ route('verification.check.form') }}" class="text-xs text-gray-400 hover:text-oren transition">
            <i class="fas fa-search mr-1"></i> Cek Status Verifikasi
        </a>
    </div>
</form>

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('passwordToggleIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection