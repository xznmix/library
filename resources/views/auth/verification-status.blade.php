@extends('auth.layouts.guest')

@section('title', 'Status Verifikasi - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Status Pendaftaran')
@section('auth-subtitle', 'Informasi status keanggotaan Anda')

@section('content')
<div class="space-y-6">

    @if($status == 'approved')
        {{-- Status DITERIMA --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-green-700 mb-2">✅ Pendaftaran DITERIMA!</h2>
            <p class="text-green-600 mb-4">{{ $message }}</p>
            <div class="bg-white rounded-lg p-4 mb-4 text-left">
                <p class="text-sm text-gray-600 mb-2"><strong>Nama:</strong> {{ $name }}</p>
                <p class="text-sm text-gray-600 mb-2"><strong>Email:</strong> {{ $email }}</p>
                @if($member_number)
                <p class="text-sm text-gray-600"><strong>Nomor Anggota:</strong> 
                    <span class="font-mono font-bold text-green-600">{{ $member_number }}</span>
                </p>
                @endif
            </div>
            <a href="{{ route('login') }}" 
               class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i>
                Login Sekarang
            </a>
        </div>

    @elseif($status == 'rejected')
        {{-- Status DITOLAK --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-red-700 mb-2">❌ Pendaftaran DITOLAK</h2>
            <p class="text-red-600 mb-4">{{ $message }}</p>
            
            @if($rejection_reason)
            <div class="bg-white rounded-lg p-4 mb-4 text-left">
                <p class="text-sm font-medium text-gray-700 mb-2">Alasan Penolakan:</p>
                <p class="text-sm text-red-600 bg-red-50 p-3 rounded-lg">{{ $rejection_reason }}</p>
            </div>
            @endif
            
            <div class="bg-yellow-50 rounded-lg p-4 mb-4 text-left">
                <p class="text-sm text-gray-700 mb-2">📌 Yang dapat Anda lakukan:</p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-edit text-yellow-600"></i>
                        Perbaiki data sesuai alasan di atas
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope text-yellow-600"></i>
                        Hubungi petugas untuk informasi lebih lanjut
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-redo-alt text-yellow-600"></i>
                        Daftar ulang dengan data yang benar
                    </li>
                </ul>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('register') }}" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Ulang
                </a>
                <a href="{{ route('verification.check.form') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all text-center">
                    <i class="fas fa-search mr-2"></i>Cek Email Lain
                </a>
            </div>
        </div>

    @else
        {{-- Status PENDING (Menunggu) --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                <i class="fas fa-clock text-yellow-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-yellow-700 mb-2">⏳ Menunggu Verifikasi</h2>
            <p class="text-yellow-600 mb-4">{{ $message }}</p>
            
            <div class="bg-white rounded-lg p-4 mb-4 text-left">
                <p class="text-sm text-gray-600 mb-2"><strong>Nama:</strong> {{ $name }}</p>
                <p class="text-sm text-gray-600"><strong>Tanggal Daftar:</strong> {{ \Carbon\Carbon::parse($created_at)->translatedFormat('d F Y H:i') }}</p>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-4 text-left">
                <p class="text-sm text-gray-700 mb-2">📌 Informasi:</p>
                <ul class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i>
                        Proses verifikasi maksimal <strong>1x24 jam</strong>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-bell text-blue-600"></i>
                        Anda akan mendapat notifikasi ke email jika sudah diverifikasi
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-key text-blue-600"></i>
                        Password default: <strong class="font-mono">NIK Anda</strong> (16 digit)
                    </li>
                </ul>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('login') }}" 
                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-xl transition-all text-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>Kembali ke Login
                </a>
                <a href="{{ route('verification.check.form') }}" 
                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all text-center">
                    <i class="fas fa-search mr-2"></i>Cek Email Lain
                </a>
            </div>
            
            <!-- Auto Refresh Script -->
            <script>
                // Auto refresh setiap 30 detik untuk cek status terbaru
                let refreshInterval = setInterval(function() {
                    fetch('{{ route("verification.check.ajax") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ email: '{{ $email }}' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'pending') {
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }, 30000);
            </script>
        </div>
    @endif

    {{-- Footer Info --}}
    <div class="text-center text-xs text-gray-500">
        <p>Perpustakaan SMAN 1 Tambang | Jl. Pekanbaru - Bangkinang KM.29</p>
        <p class="mt-1">📞 (0761) 12345 | ✉️ perpus@sman1tambang.sch.id</p>
    </div>
</div>
@endsection