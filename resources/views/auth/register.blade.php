@extends('auth.layouts.guest')

@section('title', 'Daftar - Perpustakaan SMAN 1 Tambang')
@section('auth-title', 'Mulai Bergabung! ✨')
@section('auth-subtitle', 'Isi data diri untuk menjadi anggota perpustakaan digital')

@section('content')
<form method="POST" action="{{ route('register.store') }}" id="registerForm" class="space-y-8" enctype="multipart/form-data">
    @csrf

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-6 py-5 rounded-xl text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>
                <p class="font-medium text-base">🎉 Pendaftaran Berhasil! 🎉</p>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-5 rounded-xl text-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-red-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-medium mb-2">Mohon periksa kembali data Anda:</p>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Info penting -->
    <div class="flex items-center gap-4 p-5 bg-blue-50 rounded-xl border border-blue-200">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-white text-xl"></i>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-700">Informasi Penting:</p>
            <p class="text-xs text-gray-500">Password awal akan menggunakan NIK Anda | Akun aktif dalam 1x24 jam</p>
        </div>
    </div>

    <!-- Nama Lengkap -->
    <div class="group">
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-user text-gray-400 mr-2"></i>
            Nama Lengkap <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12 @error('name') border-red-500 @enderror"
                   placeholder="Contoh: Ilvi Maulidya Nurulisa">
            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
        </div>
        @error('name')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Grid NIK dan Email -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- NIK -->
        <div class="group">
            <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-id-card text-gray-400 mr-2"></i>
                NIK <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input id="nik" 
                       type="text" 
                       name="nik" 
                       value="{{ old('nik') }}" 
                       required 
                       class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12 @error('nik') border-red-500 @enderror"
                       placeholder="16 digit NIK"
                       maxlength="16"
                       pattern="[0-9]{16}">
                <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <i class="fas fa-key text-orange-500 text-xs"></i>
                Akan digunakan sebagai password awal
            </p>
            @error('nik')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="group">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                Email <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12 @error('email') border-red-500 @enderror"
                       placeholder="contoh@email.com">
                <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
            @error('email')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Grid Phone dan Pekerjaan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- No. Telepon -->
        <div class="group">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fab fa-whatsapp text-gray-400 mr-2"></i>
                No. WhatsApp
            </label>
            <div class="relative">
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12"
                       placeholder="08123456789">
                <i class="fab fa-whatsapp absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                <i class="fas fa-bell text-orange-500 text-xs"></i>
                Untuk notifikasi penting
            </p>
        </div>

        <!-- Pekerjaan -->
        <div class="group">
            <label for="pekerjaan" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                Pekerjaan
            </label>
            <div class="relative">
                <input type="text" 
                       id="pekerjaan" 
                       name="pekerjaan" 
                       value="{{ old('pekerjaan') }}"
                       class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12"
                       placeholder="Guru, Wiraswasta, Mahasiswa, dll">
                <i class="fas fa-briefcase absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- Alamat Lengkap -->
    <div class="group">
        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
            Alamat Lengkap
        </label>
        <div class="relative">
            <textarea id="address" 
                      name="address" 
                      rows="3"
                      class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12 resize-none"
                      placeholder="Masukkan alamat lengkap sesuai KTP (Jalan, RT/RW, Kelurahan, Kecamatan, Kabupaten/Kota)">{{ old('address') }}</textarea>
            <i class="fas fa-map-marker-alt absolute left-4 top-4 text-gray-400 text-lg"></i>
        </div>
    </div>

    <!-- Upload Foto KTP -->
    <div class="group">
        <label for="foto_ktp" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-id-card text-gray-400 mr-2"></i>
            Upload Foto KTP <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input type="file" 
                   id="foto_ktp" 
                   name="foto_ktp" 
                   required 
                   accept="image/jpeg,image/png,image/jpg"
                   class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all text-base pl-12 @error('foto_ktp') border-red-500 @enderror"
                   onchange="previewImage(this)">
            <i class="fas fa-file-image absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
        </div>
        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
            <i class="fas fa-info-circle text-orange-500 text-xs"></i>
            Format: JPG, JPEG, PNG. Maksimal 2MB
        </p>
        <!-- Preview image -->
        <div id="filePreview" class="hidden mt-4">
            <img id="previewImage" class="h-28 w-auto rounded-lg border border-gray-200 shadow-sm" alt="Preview KTP">
            <p id="fileName" class="text-xs text-orange-500 font-medium mt-2"></p>
        </div>
        @error('foto_ktp')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror
    </div>

    <!-- Hidden field untuk jenis anggota -->
    <input type="hidden" name="jenis" value="umum">

    <!-- Agreement Checkbox -->
    <div class="flex items-start gap-3 p-5 bg-gray-50 rounded-xl border border-gray-200">
        <input type="checkbox" id="terms" required class="mt-0.5 w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-orange-500">
        <label for="terms" class="text-sm text-gray-600 leading-relaxed">
            Saya menyetujui <a href="#" class="text-orange-500 hover:underline font-medium">Syarat & Ketentuan</a> dan 
            <a href="#" class="text-orange-500 hover:underline font-medium">Kebijakan Privasi</a> yang berlaku. 
            Data saya akan digunakan sesuai dengan ketentuan yang berlaku.
        </label>
    </div>

    <!-- Tombol Register -->
    <button type="submit" id="submitBtn" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 rounded-xl transition-all flex items-center justify-center gap-3 group text-base shadow-lg hover:shadow-xl">
        <i class="fas fa-user-plus text-lg"></i>
        <span id="btnText">Daftar Sekarang</span>
        <i class="fas fa-arrow-right opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
    </button>

    <!-- Link Login -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">Sudah punya akun?</span>
        </div>
    </div>

    <a href="{{ route('login') }}" 
       class="w-full bg-gray-50 border-2 border-orange-200 hover:border-orange-500 text-gray-700 font-semibold py-4 rounded-xl transition-all flex items-center justify-center gap-3 group hover:shadow-lg text-base">
        <i class="fas fa-sign-in-alt text-orange-500 text-lg group-hover:scale-110 transition-transform"></i>
        <span>Masuk ke Akun Saya</span>
        <i class="fas fa-arrow-right text-orange-500 opacity-0 group-hover:opacity-100 transition-all transform group-hover:translate-x-1"></i>
    </a>

    <!-- Benefit badges -->
    <div class="flex flex-wrap justify-center gap-3 mt-6 pt-2">
        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-medium flex items-center gap-2">
            <i class="fas fa-bolt text-orange-500"></i> Akses 24/7
        </span>
        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-medium flex items-center gap-2">
            <i class="fas fa-book text-orange-500"></i> 1000+ Buku
        </span>
        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-medium flex items-center gap-2">
            <i class="fas fa-file-pdf text-orange-500"></i> E-Book Gratis
        </span>
        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-medium flex items-center gap-2">
            <i class="fas fa-headset text-orange-500"></i> Support 24/7
        </span>
        <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-xs font-medium flex items-center gap-2">
            <i class="fas fa-gem text-orange-500"></i> Free Member
        </span>
    </div>

    <!-- Quote -->
    <p class="text-center text-xs text-gray-400 mt-6 pt-2 border-t border-gray-100">
        <i class="fas fa-quote-left mr-1 text-orange-500"></i>
        Bergabunglah dengan ribuan anggota lainnya dan nikmati kemudahan akses perpustakaan digital.
        <i class="fas fa-quote-right ml-1 text-orange-500"></i>
    </p>
</form>

<script>
function previewImage(input) {
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileName = document.getElementById('fileName');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            filePreview.classList.remove('hidden');
            fileName.textContent = input.files[0].name;
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        filePreview.classList.add('hidden');
    }
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    const terms = document.getElementById('terms');
    if (!terms.checked) {
        e.preventDefault();
        alert('Harap centang persetujuan Syarat & Ketentuan terlebih dahulu.');
        return false;
    }
    
    const nik = document.getElementById('nik');
    if (nik.value && nik.value.length !== 16) {
        e.preventDefault();
        alert('NIK harus terdiri dari 16 digit angka.');
        nik.focus();
        return false;
    }
    
    const ktpFile = document.getElementById('foto_ktp');
    if (ktpFile.files.length === 0) {
        e.preventDefault();
        alert('Silakan upload foto KTP.');
        return false;
    }
    
    const fileSize = ktpFile.files[0].size / 1024 / 1024;
    if (fileSize > 2) {
        e.preventDefault();
        alert('Ukuran file maksimal 2MB. Silakan kompres gambar Anda.');
        return false;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    if (submitBtn && btnText) {
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        submitBtn.disabled = true;
    }
    
    return true;
});

document.getElementById('nik')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
});

document.getElementById('phone')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15);
});
</script>

<style>
.group:hover .form-label i {
    color: #F97316;
}

input[type="file"] {
    cursor: pointer;
}

input[type="file"]::file-selector-button {
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    margin-right: 16px;
}

input[type="file"]::file-selector-button:hover {
    background: linear-gradient(135deg, #2563EB, #1D4ED8);
    transform: translateY(-1px);
}

input[type="checkbox"] {
    cursor: pointer;
}

input[type="checkbox"]:checked {
    background-color: #F97316;
    border-color: #F97316;
}
</style>
@endsection