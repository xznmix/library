@extends('auth.layouts.guest')

@section('title', 'Menunggu Verifikasi - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Pendaftaran Berhasil! 🎉')
@section('auth-subtitle', 'Silakan tunggu verifikasi dari petugas')

@section('content')
<div class="text-center space-y-6">
    <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto animate-pulse">
        <i class="fas fa-clock text-yellow-600 text-4xl"></i>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-800">Menunggu Verifikasi Petugas</h2>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-left">
        <p class="text-gray-700 mb-3">
            Terima kasih <strong>{{ session('pending_email') }}</strong> telah mendaftar!
        </p>
        <p class="text-gray-700 mb-3">
            Akun Anda akan segera diverifikasi oleh petugas perpustakaan. 
            Proses verifikasi maksimal <strong class="text-yellow-700">1x24 jam</strong>.
        </p>
        <p class="text-gray-700">Setelah disetujui, Anda akan mendapatkan:</p>
        <ul class="mt-2 space-y-2">
            <li class="flex items-center gap-2 text-sm">
                <i class="fas fa-id-card text-green-500"></i> Nomor Anggota digital
            </li>
            <li class="flex items-center gap-2 text-sm">
                <i class="fas fa-book text-green-500"></i> Akses peminjaman buku
            </li>
            <li class="flex items-center gap-2 text-sm">
                <i class="fas fa-file-pdf text-green-500"></i> Akses koleksi digital
            </li>
        </ul>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-left">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Informasi Login:</p>
                <ul class="space-y-1">
                    <li>🔑 Password default: <strong>NIK Anda</strong></li>
                    <li>📧 Email: <strong>{{ session('pending_email') }}</strong></li>
                    <li>⚠️ Segera ganti password setelah login pertama</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="space-y-3">
        <a href="mailto:perpustakaan@sman1tambang.sch.id" 
           class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
            <i class="fas fa-envelope"></i> Hubungi Petugas
        </a>
        
        <div class="flex gap-3">
            <a href="{{ route('verification.check.form') }}" 
               class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center">
                <i class="fas fa-search mr-2"></i> Cek Status
            </a>
            <a href="{{ route('login') }}" 
               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all text-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </a>
        </div>
    </div>
</div>

<script>
    // Cek status otomatis setiap 30 detik
    let checkInterval = setInterval(function() {
        fetch('{{ route("verification.check.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: '{{ session('pending_email') }}' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'pending') {
                clearInterval(checkInterval);
                window.location.href = '{{ route("verification.status", ["email" => session('pending_email')]) }}';
            }
        })
        .catch(error => console.error('Error:', error));
    }, 30000);
</script>
@endsection