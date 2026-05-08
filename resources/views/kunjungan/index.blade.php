<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Kunjungan Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #1F2937 0%, #2563EB 100%);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        .btn-primary {
            background-color: #3B82F6;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-primary:hover {
            background-color: #2563EB;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #F97316;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #EA580C;
            transform: translateY(-2px);
        }
        .btn-success {
            background-color: #10B981;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-block bg-white p-4 rounded-2xl shadow-lg mb-4">
                <svg class="w-20 h-20 text-blue-600" fill="none" stroke="#3B82F6" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">PERPUSTAKAAN SMAN 1 TAMBANG</h1>
            <p class="text-blue-200 text-lg">Absensi Kunjungan Pengunjung</p>
        </div>

        {{-- Main Card --}}
        <div class="glass-effect rounded-2xl shadow-2xl p-8 animate-fadeIn">
            
            {{-- Pilihan Role --}}
            <div class="flex gap-4 mb-8">
                <button onclick="pilihRole('anggota')" 
                        id="btnAnggota"
                        class="flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-blue-600 text-white border-blue-600">
                    Anggota Terdaftar
                </button>
                <button onclick="pilihRole('pemustaka')" 
                        id="btnPemustaka"
                        class="flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-gray-200 text-gray-800 border-gray-300 hover:bg-gray-300">
                    Pemustaka Baru
                </button>
            </div>

            {{-- FORM ANGGOTA --}}
            <div id="formAnggota" class="space-y-6">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Input NISN / NIK</h2>
                
                <div class="relative max-w-md mx-auto">
                    <input type="text" 
                           id="nisnInput"
                           class="w-full px-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg text-center text-gray-800"
                           placeholder="Masukkan NISN atau NIK"
                           autofocus>
                    
                    <button onclick="cariAnggota()" 
                            class="absolute right-2 top-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all">
                        Cari
                    </button>
                </div>

                {{-- Loading --}}
                <div id="loadingAnggota" class="hidden text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                    <p class="mt-2 text-gray-700">Mencari data...</p>
                </div>

                {{-- Hasil Pencarian Anggota --}}
                <div id="hasilAnggota" class="hidden slide-in">
                    <div class="bg-blue-50 rounded-xl p-6 border-2 border-blue-200">
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 overflow-hidden border-4 border-white shadow-lg">
                                <img id="anggotaFoto" src="" alt="Foto" class="w-full h-full object-cover hidden">
                                <div id="anggotaInisial" class="w-full h-full flex items-center justify-center text-3xl font-bold text-blue-600 bg-blue-100">
                                    -
                                </div>
                            </div>

                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-800" id="anggotaNama"></h3>
                                <div class="grid grid-cols-2 gap-2 mt-3">
                                    <div>
                                        <p class="text-xs text-gray-600">NISN/NIK</p>
                                        <p class="font-mono text-gray-800" id="anggotaNisn"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Jenis</p>
                                        <p class="capitalize text-gray-800" id="anggotaJenis"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Kunjungan Ke-</p>
                                        <p class="text-2xl font-bold text-blue-600" id="anggotaKe"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Kelas</p>
                                        <p class="text-gray-800" id="anggotaKelas"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button onclick="konfirmasiMasuk()" 
                                class="mt-6 w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold text-lg transition-all">
                            ✅ Konfirmasi Masuk
                        </button>
                    </div>
                </div>

                <div id="anggotaNotFound" class="hidden bg-orange-50 border-2 border-orange-200 rounded-xl p-4 text-center">
                    <svg class="w-12 h-12 text-orange-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-orange-700 font-medium" id="notFoundMessage">NISN/NIK tidak ditemukan</p>
                    <button onclick="pilihRole('pemustaka')" class="mt-2 text-blue-600 hover:text-blue-800 font-medium">
                        Daftar sebagai pemustaka baru?
                    </button>
                </div>
            </div>

            {{-- FORM PEMUSTAKA BARU --}}
            <div id="formPemustaka" class="hidden space-y-4">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Form Kunjungan Tamu</h2>
                
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="pemustakaNama" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-800">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <input type="text" value="UMUM" readonly
                               class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed">
                        <input type="hidden" id="pemustakaJenis" value="umum">
                        <p class="text-xs text-gray-500 mt-1">Pemustaka baru otomatis sebagai anggota umum</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No. HP <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="pemustakaNoHp" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-800"
                               placeholder="Contoh: 08123456789">
                        <p class="text-xs text-gray-500 mt-1">Untuk notifikasi dan informasi penting</p>
                    </div>
                </div>

                <button onclick="simpanPemustaka()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold text-lg transition-all">
                    ✅ Konfirmasi Kunjungan
                </button>
            </div>

            {{-- Loading Global --}}
            <div id="loadingGlobal" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"></div>
                <p class="mt-4 text-gray-700 text-lg">Memproses...</p>
            </div>

            {{-- Hasil Sukses (hidden, untuk fallback) --}}
            <div id="hasilSukses" class="hidden text-center py-8">
                <div class="bg-green-50 text-green-700 p-8 rounded-xl mb-6 border-2 border-green-200">
                    <svg class="w-20 h-20 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-2xl font-bold mb-2" id="successMessage"></p>
                    <p class="text-lg" id="successDetail"></p>
                </div>
                <button onclick="resetForm()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold transition-all">
                    Absensi Lagi
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-center text-gray-200 text-sm mt-6">
            &copy; {{ date('Y') }} Perpustakaan SMAN 1 Tambang
        </p>
    </div>

    <script>
        let dataAnggota = null;

    // Toggle role
    function pilihRole(role) {
        const btnAnggota = document.getElementById('btnAnggota');
        const btnPemustaka = document.getElementById('btnPemustaka');
        const formAnggota = document.getElementById('formAnggota');
        const formPemustaka = document.getElementById('formPemustaka');

        if (!btnAnggota || !btnPemustaka || !formAnggota || !formPemustaka) {
            console.error('Elements not found');
            return;
        }

        if (role === 'anggota') {
            btnAnggota.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-blue-600 text-white border-blue-600';
            btnPemustaka.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-gray-200 text-gray-800 border-gray-300 hover:bg-gray-300';
            formAnggota.classList.remove('hidden');
            formPemustaka.classList.add('hidden');
            const nisnInput = document.getElementById('nisnInput');
            if (nisnInput) nisnInput.focus();
            resetForm();
        } else {
            btnPemustaka.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-green-600 text-white border-green-600';
            btnAnggota.className = 'flex-1 py-3 px-4 rounded-xl font-semibold transition-all border-2 bg-gray-200 text-gray-800 border-gray-300 hover:bg-gray-300';
            formPemustaka.classList.remove('hidden');
            formAnggota.classList.add('hidden');
            // Reset form pemustaka
            const pemustakaNama = document.getElementById('pemustakaNama');
            const pemustakaNoHp = document.getElementById('pemustakaNoHp');
            if (pemustakaNama) pemustakaNama.value = '';
            if (pemustakaNoHp) pemustakaNoHp.value = '';
        }
    }

    // NOTIFIKASI SUKSES UNTUK ANGGOTA TERDAFTAR
    function showSuccessNotification(nama, jam, kunjunganKe) {
        Swal.fire({
            icon: 'success',
            title: 'Selamat Datang!',
            html: `
                <div class="text-center">
                    <div class="text-6xl mb-3 animate-bounce">🎉</div>
                    <p class="text-xl font-bold text-gray-800">Halo, <span class="text-blue-600">${nama}</span>!</p>
                    <div class="mt-5 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
                        <div class="flex items-center justify-center gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Jam Masuk</p>
                                <p class="text-lg font-bold text-blue-600">${jam}</p>
                            </div>
                            <div class="w-px h-8 bg-gray-300"></div>
                            <div class="text-center">
                                <p class="text-xs text-gray-600">Kunjungan Ke-</p>
                                <p class="text-lg font-bold text-green-600">${kunjunganKe}</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-3">Selamat membaca dan belajar di perpustakaan kami! 📚</p>
                </div>
            `,
            confirmButtonColor: '#3B82F6',
            confirmButtonText: '👍 Lanjutkan',
            timer: 10000,
            timerProgressBar: true,
            backdrop: true,
            allowOutsideClick: false
        }).then(() => {
            location.reload();
        });
    }

    // Cari anggota
    function cariAnggota() {
        const nisnInput = document.getElementById('nisnInput');
        if (!nisnInput) return;
        
        const nisn = nisnInput.value.trim();

        if (!nisn) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Masukkan NISN/NIK terlebih dahulu',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }

        const loadingAnggota = document.getElementById('loadingAnggota');
        const hasilAnggota = document.getElementById('hasilAnggota');
        const anggotaNotFound = document.getElementById('anggotaNotFound');
        
        if (loadingAnggota) loadingAnggota.classList.remove('hidden');
        if (hasilAnggota) hasilAnggota.classList.add('hidden');
        if (anggotaNotFound) anggotaNotFound.classList.add('hidden');

        fetch('/kunjungan/cari-anggota', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nisn: nisn })
        })
        .then(response => response.json())
        .then(data => {
            if (loadingAnggota) loadingAnggota.classList.add('hidden');

            if (data.found) {
                dataAnggota = data.data;
                
                const anggotaFoto = document.getElementById('anggotaFoto');
                const anggotaInisial = document.getElementById('anggotaInisial');
                const anggotaNama = document.getElementById('anggotaNama');
                const anggotaNisn = document.getElementById('anggotaNisn');
                const anggotaJenis = document.getElementById('anggotaJenis');
                const anggotaKe = document.getElementById('anggotaKe');
                const anggotaKelas = document.getElementById('anggotaKelas');
                
                if (data.data.foto && anggotaFoto) {
                    anggotaFoto.src = data.data.foto;
                    anggotaFoto.classList.remove('hidden');
                    if (anggotaInisial) anggotaInisial.classList.add('hidden');
                } else {
                    if (anggotaFoto) anggotaFoto.classList.add('hidden');
                    if (anggotaInisial) {
                        anggotaInisial.classList.remove('hidden');
                        anggotaInisial.textContent = data.data.nama.charAt(0);
                    }
                }

                if (anggotaNama) anggotaNama.textContent = data.data.nama;
                if (anggotaNisn) anggotaNisn.textContent = data.data.nisn;
                if (anggotaJenis) anggotaJenis.textContent = data.data.jenis;
                if (anggotaKe) anggotaKe.textContent = data.data.kunjungan_ke;
                if (anggotaKelas) anggotaKelas.textContent = data.data.kelas || '-';

                if (hasilAnggota) hasilAnggota.classList.remove('hidden');
            } else {
                const notFoundMessage = document.getElementById('notFoundMessage');
                if (notFoundMessage) notFoundMessage.textContent = data.message;
                if (anggotaNotFound) anggotaNotFound.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingAnggota) loadingAnggota.classList.add('hidden');
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: error.message,
                confirmButtonColor: '#3B82F6'
            });
        });
    }

    // Konfirmasi masuk anggota
    function konfirmasiMasuk() {
        if (!dataAnggota) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Data anggota tidak ditemukan, silakan cari terlebih dahulu',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }

        const loadingGlobal = document.getElementById('loadingGlobal');
        const hasilAnggota = document.getElementById('hasilAnggota');
        
        if (loadingGlobal) loadingGlobal.classList.remove('hidden');
        if (hasilAnggota) hasilAnggota.classList.add('hidden');

        fetch('/kunjungan/anggota', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ user_id: dataAnggota.id })
        })
        .then(response => response.json())
        .then(data => {
            if (loadingGlobal) loadingGlobal.classList.add('hidden');

            if (data.success) {
                showSuccessNotification(data.data.nama, data.data.jam, data.data.kunjungan_ke);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    confirmButtonColor: '#3B82F6'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingGlobal) loadingGlobal.classList.add('hidden');
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: error.message,
                confirmButtonColor: '#3B82F6'
            });
        });
    }

    // Simpan pemustaka baru (NOTIFIKASI SAMA DENGAN ANGGOTA)
    function simpanPemustaka() {
        const namaInput = document.getElementById('pemustakaNama');
        const noHpInput = document.getElementById('pemustakaNoHp');
        
        if (!namaInput || !noHpInput) return;
        
        const nama = namaInput.value.trim();
        const noHp = noHpInput.value.trim();

        if (!nama) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Nama harus diisi',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }

        if (!noHp) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                text: 'Nomor HP harus diisi',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }

        const data = {
            nama: nama,
            jenis: 'umum',
            no_hp: noHp,
        };

        const loadingGlobal = document.getElementById('loadingGlobal');
        const formPemustaka = document.getElementById('formPemustaka');
        
        if (loadingGlobal) loadingGlobal.classList.remove('hidden');
        if (formPemustaka) formPemustaka.classList.add('hidden');

        fetch('/kunjungan/pemustaka', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (loadingGlobal) loadingGlobal.classList.add('hidden');

            if (data.success) {
                // NOTIFIKASI SAMA DENGAN ANGGOTA TERDAFTAR
                showSuccessNotification(data.data.nama, data.data.jam, data.kunjungan_ke);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    confirmButtonColor: '#3B82F6'
                });
                if (formPemustaka) formPemustaka.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingGlobal) loadingGlobal.classList.add('hidden');
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal menyimpan data. Silakan coba lagi.',
                confirmButtonColor: '#3B82F6'
            });
            if (formPemustaka) formPemustaka.classList.remove('hidden');
        });
    }

    // Reset form
    function resetForm() {
        const hasilSukses = document.getElementById('hasilSukses');
        const formAnggota = document.getElementById('formAnggota');
        const nisnInput = document.getElementById('nisnInput');
        const hasilAnggota = document.getElementById('hasilAnggota');
        const anggotaNotFound = document.getElementById('anggotaNotFound');
        
        if (hasilSukses) hasilSukses.classList.add('hidden');
        if (formAnggota) formAnggota.classList.remove('hidden');
        if (nisnInput) {
            nisnInput.value = '';
            nisnInput.focus();
        }
        if (hasilAnggota) hasilAnggota.classList.add('hidden');
        if (anggotaNotFound) anggotaNotFound.classList.add('hidden');
        dataAnggota = null;
    }

    // Enter key untuk cari anggota
    const nisnInput = document.getElementById('nisnInput');
    if (nisnInput) {
        nisnInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                cariAnggota();
            }
        });
    }
    </script>
</body>
</html>