@extends('petugas.layouts.app')

@section('title', 'Tambah Baca di Tempat')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto">

    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.baca-ditempat.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📖 Baca di Tempat</h1>
                <p class="text-sm text-gray-500 mt-1">Catat aktivitas membaca anggota di perpustakaan</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('petugas.baca-ditempat.store') }}" method="POST" id="formBaca" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        <input type="hidden" name="user_id" id="user_id">
        <input type="hidden" name="buku_id" id="buku_id">

        <div class="space-y-6">
            {{-- Pilih Anggota --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="text-red-500">*</span> Cari Anggota
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                            id="searchAnggota" 
                            placeholder="Ketik Nama / NISN / No. Anggota / Email (min 2 karakter)" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                            autocomplete="off">
                        <div id="anggotaResults" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <button type="button" id="btnCariAnggota" class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Cari
                    </button>
                </div>
                <div id="selectedAnggotaInfo" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-green-800">Anggota Terpilih:</p>
                            <p class="text-sm text-green-700">
                                <span id="selectedAnggotaNama"></span> - 
                                <span id="selectedAnggotaKelas"></span><br>
                                <span class="text-xs">No. Anggota: <span id="selectedAnggotaNo"></span></span>
                            </p>
                        </div>
                        <button type="button" id="btnResetAnggota" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Pilih Buku --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="text-red-500">*</span> Cari Buku
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                            id="searchBuku" 
                            placeholder="Ketik Judul / Pengarang / Barcode (min 2 karakter)" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                            autocomplete="off">
                        <div id="bukuResults" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <button type="button" id="btnCariBuku" class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Cari
                    </button>
                </div>
                <div id="selectedBukuInfo" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold text-green-800">Buku Terpilih:</p>
                            <p class="text-sm text-green-700">
                                <span id="selectedBukuJudul"></span><br>
                                <span class="text-xs">Pengarang: <span id="selectedBukuPengarang"></span></span>
                            </p>
                        </div>
                        <button type="button" id="btnResetBuku" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                <input type="text" name="lokasi" value="Perpustakaan Tambang Ilmu - Ruang Baca Umum" 
                       class="w-full px-4 py-3 border rounded-lg bg-gray-50 text-gray-600">
                <p class="text-xs text-gray-500 mt-1">Lokasi dapat diubah jika diperlukan</p>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                <textarea name="catatan" rows="3" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500" 
                          placeholder="Contoh: Mengerjakan tugas / Membaca untuk lomba"></textarea>
            </div>

            {{-- Info Poin --}}
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <div class="text-2xl">⭐</div>
                    <div>
                        <p class="font-semibold text-yellow-800 mb-2">Sistem Perolehan Poin:</p>
                        <div class="space-y-1 text-sm">
                            <p class="text-yellow-700">✓ Poin Dasar: <strong>5 poin</strong> (setiap membaca)</p>
                            <p class="text-yellow-700">✓ Bonus +5 poin (membaca ≥ 30 menit)</p>
                            <p class="text-yellow-700">✓ Bonus +5 poin (membaca ≥ 60 menit)</p>
                            <p class="text-yellow-800 font-semibold mt-2">Maksimal poin: 15 poin per sesi baca</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('petugas.baca-ditempat.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="button" onclick="verifikasiData()" class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    Verifikasi & Mulai Baca
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Modal Verifikasi --}}
<div id="verifikasiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Verifikasi Data</h3>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <table class="w-full text-sm">
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">Anggota</td>
                    <td class="py-2" id="verifAnggota">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">No. Anggota</td>
                    <td class="py-2 font-mono text-xs" id="verifNoAnggota">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">Kelas</td>
                    <td class="py-2" id="verifKelas">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">Buku</td>
                    <td class="py-2" id="verifBuku">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">Pengarang</td>
                    <td class="py-2" id="verifPengarang">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600 font-medium">Lokasi</td>
                    <td class="py-2" id="verifLokasi">-</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-600 font-medium">Catatan</td>
                    <td class="py-2" id="verifCatatan">-</td>
                </tr>
            </table>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-700">
                <strong>⚠️ Perhatian!</strong> Pastikan data sudah benar. 
                Data yang sudah disimpan tidak dapat diubah.
            </p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button type="button" onclick="tutupModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                Ya, Mulai Baca
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedAnggotaData = null;
let selectedBukuData = null;

document.addEventListener('DOMContentLoaded', function() {
    const searchAnggota = document.getElementById('searchAnggota');
    const anggotaResults = document.getElementById('anggotaResults');
    const btnCariAnggota = document.getElementById('btnCariAnggota');
    const btnResetAnggota = document.getElementById('btnResetAnggota');
    const selectedAnggotaInfo = document.getElementById('selectedAnggotaInfo');

    const searchBuku = document.getElementById('searchBuku');
    const bukuResults = document.getElementById('bukuResults');
    const btnCariBuku = document.getElementById('btnCariBuku');
    const btnResetBuku = document.getElementById('btnResetBuku');
    const selectedBukuInfo = document.getElementById('selectedBukuInfo');

    function resetAnggota() {
        searchAnggota.value = '';
        searchAnggota.disabled = false;
        searchAnggota.classList.remove('bg-gray-100');
        document.getElementById('user_id').value = '';
        selectedAnggotaInfo.classList.add('hidden');
        selectedAnggotaData = null;
        searchAnggota.focus();
        anggotaResults.classList.add('hidden');
    }

    function cariAnggota() {
        const query = searchAnggota.value.trim();
        if (query.length < 2) {
            alert('Minimal 2 karakter untuk mencari anggota');
            return;
        }
        
        // Show loading
        btnCariAnggota.disabled = true;
        btnCariAnggota.textContent = 'Mencari...';
        
        fetch('/petugas/baca-ditempat/cari-anggota?search=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Anggota tidak ditemukan');
                    return;
                }
                
                if (data.data.length === 0) {
                    alert('Anggota tidak ditemukan');
                    return;
                }
                
                let html = '<div class="p-2 font-semibold bg-gray-100 sticky top-0">📋 Hasil Pencarian (' + data.data.length + '):</div>';
                data.data.forEach(a => {
                    html += `<div class="p-3 hover:bg-indigo-50 cursor-pointer border-b anggota-item" 
                                 data-id="${a.id}"
                                 data-nama="${a.nama}"
                                 data-no="${a.no_anggota}"
                                 data-kelas="${a.kelas}">
                        <div class="font-medium text-gray-800">👤 ${a.nama}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            🆔 ${a.no_anggota} | 📚 ${a.kelas} | ⭐ ${a.poin} poin
                        </div>
                    </div>`;
                });
                anggotaResults.innerHTML = html;
                anggotaResults.classList.remove('hidden');
                
                // Add click handlers
                document.querySelectorAll('.anggota-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const nama = this.dataset.nama;
                        const noAnggota = this.dataset.no;
                        const kelas = this.dataset.kelas;
                        
                        selectedAnggotaData = { id, nama, noAnggota, kelas };
                        
                        searchAnggota.value = `${nama} (${kelas}) - ${noAnggota}`;
                        searchAnggota.disabled = true;
                        searchAnggota.classList.add('bg-gray-100');
                        document.getElementById('user_id').value = id;
                        
                        document.getElementById('selectedAnggotaNama').innerHTML = `<strong>${nama}</strong>`;
                        document.getElementById('selectedAnggotaKelas').innerHTML = kelas;
                        document.getElementById('selectedAnggotaNo').innerHTML = noAnggota;
                        selectedAnggotaInfo.classList.remove('hidden');
                        
                        anggotaResults.classList.add('hidden');
                    });
                });
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Gagal memuat data anggota');
            })
            .finally(() => {
                btnCariAnggota.disabled = false;
                btnCariAnggota.textContent = 'Cari';
            });
    }

    function resetBuku() {
        searchBuku.value = '';
        searchBuku.disabled = false;
        searchBuku.classList.remove('bg-gray-100');
        document.getElementById('buku_id').value = '';
        selectedBukuInfo.classList.add('hidden');
        selectedBukuData = null;
        searchBuku.focus();
        bukuResults.classList.add('hidden');
    }

    function cariBuku() {
        const query = searchBuku.value.trim();
        if (query.length < 2) {
            alert('Minimal 2 karakter untuk mencari buku');
            return;
        }
        
        btnCariBuku.disabled = true;
        btnCariBuku.textContent = 'Mencari...';
        
        fetch('/petugas/baca-ditempat/cari-buku?search=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Buku tidak ditemukan');
                    return;
                }
                
                if (data.data.length === 0) {
                    alert('Buku tidak ditemukan');
                    return;
                }
                
                let html = '<div class="p-2 font-semibold bg-gray-100 sticky top-0">📚 Hasil Pencarian (' + data.data.length + '):</div>';
                data.data.forEach(b => {
                    html += `<div class="p-3 hover:bg-purple-50 cursor-pointer border-b buku-item" 
                                 data-id="${b.id}"
                                 data-judul="${b.judul}"
                                 data-pengarang="${b.pengarang}">
                        <div class="font-medium text-gray-800">📖 ${b.judul}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            ✍️ ${b.pengarang} | 🏢 ${b.penerbit} | 📍 Rak ${b.rak}
                        </div>
                    </div>`;
                });
                bukuResults.innerHTML = html;
                bukuResults.classList.remove('hidden');
                
                document.querySelectorAll('.buku-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const judul = this.dataset.judul;
                        const pengarang = this.dataset.pengarang;
                        
                        selectedBukuData = { id, judul, pengarang };
                        
                        searchBuku.value = `${judul} - ${pengarang}`;
                        searchBuku.disabled = true;
                        searchBuku.classList.add('bg-gray-100');
                        document.getElementById('buku_id').value = id;
                        
                        document.getElementById('selectedBukuJudul').innerHTML = `<strong>${judul}</strong>`;
                        document.getElementById('selectedBukuPengarang').innerHTML = pengarang;
                        selectedBukuInfo.classList.remove('hidden');
                        
                        bukuResults.classList.add('hidden');
                    });
                });
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Gagal memuat data buku');
            })
            .finally(() => {
                btnCariBuku.disabled = false;
                btnCariBuku.textContent = 'Cari';
            });
    }

    btnCariAnggota.addEventListener('click', cariAnggota);
    btnResetAnggota.addEventListener('click', resetAnggota);
    btnCariBuku.addEventListener('click', cariBuku);
    btnResetBuku.addEventListener('click', resetBuku);
    
    searchAnggota.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cariAnggota();
        }
    });
    
    searchBuku.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cariBuku();
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!searchAnggota.contains(e.target) && !anggotaResults.contains(e.target)) {
            anggotaResults.classList.add('hidden');
        }
        if (!searchBuku.contains(e.target) && !bukuResults.contains(e.target)) {
            bukuResults.classList.add('hidden');
        }
    });
});

function verifikasiData() {
    const userId = document.getElementById('user_id').value;  // ← Perbaiki dari anggotaId ke userId
    
    if (!userId) {  // ← Perbaiki dari !anggotaId ke !userId
        alert('Silakan pilih anggota terlebih dahulu!');
        document.getElementById('searchAnggota').focus();
        return;
    }
    
    const bukuId = document.getElementById('buku_id').value;
    
    if (!bukuId) {
        alert('Silakan pilih buku terlebih dahulu!');
        document.getElementById('searchBuku').focus();
        return;
    }
    
    // Set modal content
    document.getElementById('verifAnggota').innerHTML = document.getElementById('selectedAnggotaNama').innerHTML || '-';
    document.getElementById('verifNoAnggota').innerHTML = document.getElementById('selectedAnggotaNo').innerHTML || '-';
    document.getElementById('verifKelas').innerHTML = document.getElementById('selectedAnggotaKelas').innerHTML || '-';
    document.getElementById('verifBuku').innerHTML = document.getElementById('selectedBukuJudul').innerHTML || '-';
    document.getElementById('verifPengarang').innerHTML = document.getElementById('selectedBukuPengarang').innerHTML || '-';
    document.getElementById('verifLokasi').innerHTML = document.querySelector('input[name="lokasi"]').value;
    document.getElementById('verifCatatan').innerHTML = document.querySelector('textarea[name="catatan"]').value || '-';
    
    document.getElementById('verifikasiModal').style.display = 'flex';
}

function tutupModal() {
    document.getElementById('verifikasiModal').style.display = 'none';
}

function submitForm() {
    document.getElementById('formBaca').submit();
}
</script>
@endpush