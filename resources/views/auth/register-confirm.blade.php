@extends('auth.layouts.guest')

@section('title', 'Konfirmasi Data - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Konfirmasi Data Diri ✨')
@section('auth-subtitle', 'Pastikan data yang Anda masukkan sudah benar sebelum melanjutkan')

@section('content')
<div class="space-y-6">
    <!-- Alert Info -->
    <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Periksa Kembali Data Anda</p>
            <p class="text-xs text-gray-500">Pastikan semua data yang Anda isikan sudah benar dan sesuai dengan dokumen resmi</p>
        </div>
    </div>

    <!-- Ringkasan Data Card -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <h3 class="font-bold text-white flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i>
                Ringkasan Data Pendaftaran
            </h3>
            <p class="text-blue-100 text-xs mt-1">Data yang akan didaftarkan sebagai anggota perpustakaan</p>
        </div>
        
        <div class="p-6 space-y-4">
            <!-- Nama Lengkap -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Nama Lengkap</p>
                    <p class="font-semibold text-gray-800 text-lg">{{ $data['name'] ?? '-' }}</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- NIK -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-id-card text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">NIK (Nomor Induk Kependudukan)</p>
                    <p class="font-semibold text-gray-800 font-mono">{{ $data['nik'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Akan digunakan sebagai password awal</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- Email -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Alamat Email</p>
                    <p class="font-semibold text-gray-800">{{ $data['email'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Digunakan untuk verifikasi dan komunikasi</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- WhatsApp -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fab fa-whatsapp text-green-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Nomor WhatsApp</p>
                    <p class="font-semibold text-gray-800">{{ $data['phone'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Untuk notifikasi penting dan informasi</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- Pekerjaan -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-briefcase text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Pekerjaan</p>
                    <p class="font-semibold text-gray-800">{{ $data['pekerjaan'] ?? '-' }}</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- Alamat -->
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt text-red-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Alamat Lengkap</p>
                    <p class="font-semibold text-gray-800 leading-relaxed">{{ $data['address'] ?? '-' }}</p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            
            <!-- Jenis Anggota -->
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users text-purple-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Jenis Anggota</p>
                    <p class="font-semibold text-gray-800">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">Anggota Umum</span>
                    </p>
                </div>
                <div class="text-green-500">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preview Foto KTP -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-5 border border-gray-200">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-id-card text-blue-600 text-lg"></i>
            <p class="text-sm font-semibold text-gray-700">Dokumen Pendukung</p>
        </div>
        
        <div class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-200">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-file-image text-blue-600 text-2xl"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-800">{{ $data['foto_ktp_original_name'] ?? 'foto_ktp.jpg' }}</p>
                <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                    <i class="fas fa-check-circle text-green-500 text-xs"></i>
                    File sudah terupload dan tersimpan sementara
                </p>
            </div>
            <div class="text-right">
                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-medium">
                    <i class="fas fa-check"></i> Tersimpan
                </span>
            </div>
        </div>
        
        @if(isset($data['foto_ktp_preview']))
        <div class="mt-3">
            <p class="text-xs text-gray-500 mb-2">Preview KTP:</p>
            <img src="{{ $data['foto_ktp_preview'] }}" alt="Preview KTP" class="h-32 w-auto rounded-lg border border-gray-200 shadow-sm">
        </div>
        @endif
    </div>
    
    <!-- Peringatan Penting -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-yellow-800 mb-2 flex items-center gap-2">
                    <i class="fas fa-shield-alt"></i>
                    Perhatian!
                </h4>
                <ul class="space-y-2 text-sm text-yellow-700">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-xs mt-1.5"></i>
                        <span>Pastikan <strong>email</strong> yang Anda masukkan aktif dan benar, karena akan digunakan untuk verifikasi</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-xs mt-1.5"></i>
                        <span><strong>NIK</strong> harus sesuai dengan KTP yang sah (16 digit)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-xs mt-1.5"></i>
                        <span>Data yang tidak valid akan menyebabkan <strong>penolakan pendaftaran</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-xs mt-1.5"></i>
                        <span>Password default menggunakan <strong>NIK</strong> Anda (16 digit)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-xs mt-1.5"></i>
                        <span>Setelah konfirmasi, data akan diproses dan <strong>akun aktif dalam 1x24 jam</strong></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Tombol Aksi -->
    <div class="flex flex-col sm:flex-row gap-3 pt-2">
        <a href="{{ route('register') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2 group">
            <i class="fas fa-edit group-hover:scale-110 transition-transform"></i>
            <span>Edit Data</span>
        </a>
        
        <form method="POST" action="{{ route('register.submit') }}" id="confirmForm" class="flex-1">
            @csrf
            <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2 group">
                <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
                <span id="btnText">Ya, Data Sudah Benar</span>
                <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
            </button>
        </form>
    </div>
    
    <!-- Catatan Keamanan -->
    <div class="flex items-center justify-center gap-2 pt-4">
        <i class="fas fa-shield-alt text-gray-400 text-sm"></i>
        <p class="text-xs text-gray-400 text-center">
            Data Anda aman dan terenkripsi. Kami tidak akan membagikan data Anda kepada pihak manapun.
        </p>
    </div>
    
    <!-- Progress Steps -->
    <div class="flex items-center justify-between pt-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
            <span class="text-sm font-medium text-blue-600">Data Diri</span>
        </div>
        <div class="flex-1 h-0.5 bg-blue-600 mx-2"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
            <span class="text-sm font-medium text-blue-600">Konfirmasi</span>
        </div>
        <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
        <div class="flex items-center gap-2 opacity-50">
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold text-sm">3</div>
            <span class="text-sm font-medium text-gray-400">Selesai</span>
        </div>
    </div>
</div>

<script>
// Form submission with loading state
document.getElementById('confirmForm')?.addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    if (submitBtn && btnText) {
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses Pendaftaran...';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.7';
    }
    
    return true;
});

// Prevent double submission
let isSubmitting = false;
document.getElementById('confirmForm')?.addEventListener('submit', function(e) {
    if (isSubmitting) {
        e.preventDefault();
        return false;
    }
    isSubmitting = true;
});
</script>

<style>
/* Additional styles */
.group:hover i {
    transform: scale(1.05);
}

button:active {
    transform: scale(0.98);
}

/* Smooth transitions */
.bg-gradient-to-r {
    transition: all 0.3s ease;
}

/* Card hover effect */
.rounded-2xl {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.rounded-2xl:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Disabled button style */
button:disabled {
    cursor: not-allowed;
}
</style>
@endsection