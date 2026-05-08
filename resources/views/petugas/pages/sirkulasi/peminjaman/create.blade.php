@extends('petugas.layouts.app')

@section('title', 'Tambah Peminjaman')

@section('content')
<div class="p-4 md:p-6 max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.sirkulasi.peminjaman.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Peminjaman
                </h1>
                <p class="text-sm text-gray-500 mt-1">Catat peminjaman buku baru</p>
            </div>
        </div>
    </div>

    {{-- Notifikasi Error --}}
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

    {{-- Form Peminjaman --}}
    <form action="{{ route('petugas.sirkulasi.peminjaman.store') }}" method="POST" id="formPeminjaman" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf

        <div class="space-y-6">

            {{-- Anggota --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="text-red-500">*</span> Nama Anggota
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                            id="searchAnggota" 
                            placeholder="Ketik Nama / NISN / NIK..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                            autocomplete="off">
                        <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}">
                        <div id="anggotaResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <button type="button" id="btnCariAnggota" class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                    <button type="button" id="btnResetAnggota" class="px-4 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 hidden items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Ganti
                    </button>
                </div>
                <div id="selectedAnggotaInfo" class="mt-2 text-sm text-green-600 hidden">
                    ✅ Anggota terpilih: <span id="selectedAnggotaNama"></span>
                </div>
                @error('user_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buku --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="text-red-500">*</span> Judul Buku
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                            id="searchBuku" 
                            placeholder="Ketik Judul / Pengarang / Penerbit / Tahun..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                            autocomplete="off">
                        <input type="hidden" name="buku_id" id="buku_id" value="{{ old('buku_id') }}">
                        <div id="bukuResults" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                    <button type="button" id="btnCariBuku" class="px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                    <button type="button" id="btnResetBuku" class="px-4 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 hidden items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Ganti
                    </button>
                </div>
                <div id="selectedBukuInfo" class="mt-2 text-sm text-green-600 hidden">
                    ✅ Buku terpilih: <span id="selectedBukuJudul"></span>
                </div>
                @error('buku_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode Eksemplar --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="text-red-500">*</span> Kode Eksemplar
                </label>
                <input type="text"
                       name="kode_eksemplar"
                       id="kodeEksemplar"
                       value="{{ old('kode_eksemplar') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                       placeholder="Contoh: EX-001"
                       required>
                @error('kode_eksemplar')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Kode unik untuk eksemplar buku ini</p>
            </div>

            {{-- Tanggal --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pinjam
                    </label>
                    <input type="date"
                           name="tanggal_pinjam"
                           id="tanggalPinjam"
                           value="{{ old('tanggal_pinjam', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Jatuh Tempo
                    </label>
                    <input type="date"
                           name="tgl_jatuh_tempo"
                           id="jatuhTempo"
                           value="{{ old('tgl_jatuh_tempo', now()->addDays(7)->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                           readonly
                           disabled>
                    <p class="text-xs text-gray-500 mt-1">* Otomatis 7 hari setelah tanggal pinjam (tidak bisa diubah)</p>
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan (Opsional)
                </label>
                <textarea name="keterangan"
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('petugas.sirkulasi.peminjaman.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="button" onclick="verifikasiData()" 
                        class="px-6 py-2.5 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verifikasi Data
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Modal Verifikasi --}}
<div id="verifikasiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Verifikasi Data Peminjaman</h3>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <table class="w-full text-sm">
                <tr class="border-b">
                    <td class="py-2 text-gray-600">Anggota</td>
                    <td class="py-2 font-medium" id="verifAnggota">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600">Buku</td>
                    <td class="py-2 font-medium" id="verifBuku">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600">Kode Eksemplar</td>
                    <td class="py-2 font-medium" id="verifKode">-</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 text-gray-600">Tanggal Pinjam</td>
                    <td class="py-2 font-medium" id="verifTglPinjam">-</td>
                </tr>
                <tr>
                    <td class="py-2 text-gray-600">Jatuh Tempo</td>
                    <td class="py-2 font-medium" id="verifJatuhTempo">-</td>
                </tr>
            </table>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-700 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span><strong>Perhatian!</strong> Data yang sudah disimpan tidak dapat diubah atau diedit.</span>
            </p>
        </div>
        
        <div class="flex justify-end gap-3">
            <button type="button" onclick="tutupModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Batal
            </button>
            <button type="button" onclick="submitForm()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Ya, Simpan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // JATUH TEMPO OTOMATIS
    // ============================================
    const tanggalPinjam = document.getElementById('tanggalPinjam');
    const jatuhTempo = document.getElementById('jatuhTempo');
    
    if (tanggalPinjam && jatuhTempo) {
        function updateJatuhTempo() {
            let tgl = new Date(tanggalPinjam.value);
            tgl.setDate(tgl.getDate() + 7);
            let tahun = tgl.getFullYear();
            let bulan = String(tgl.getMonth() + 1).padStart(2, '0');
            let hari = String(tgl.getDate()).padStart(2, '0');
            jatuhTempo.value = `${tahun}-${bulan}-${hari}`;
        }
        tanggalPinjam.addEventListener('change', updateJatuhTempo);
    }

    // ============================================
    // ANGGOTA
    // ============================================
    const searchAnggota = document.getElementById('searchAnggota');
    const anggotaResults = document.getElementById('anggotaResults');
    const userIdInput = document.getElementById('user_id');
    const btnCariAnggota = document.getElementById('btnCariAnggota');
    const btnResetAnggota = document.getElementById('btnResetAnggota');
    const selectedAnggotaInfo = document.getElementById('selectedAnggotaInfo');
    const selectedAnggotaNama = document.getElementById('selectedAnggotaNama');

    function resetAnggota() {
        searchAnggota.value = '';
        searchAnggota.readOnly = false;
        searchAnggota.classList.remove('bg-gray-100', 'cursor-not-allowed');
        userIdInput.value = '';
        selectedAnggotaInfo.classList.add('hidden');
        btnCariAnggota.style.display = 'flex';
        btnResetAnggota.classList.add('hidden');
        searchAnggota.focus();
    }

    function cariAnggota() {
        const query = searchAnggota.value.trim();

        if (query.length < 2) {
            alert('Minimal 2 karakter untuk pencarian');
            return;
        }

        fetch(`{{ route('petugas.sirkulasi.cari-anggota') }}?q=${encodeURIComponent(query)}`,{
            headers:{
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            }
        })
        .then(async response => {

            if(!response.ok){
                throw new Error('Response gagal');
            }

            return response.json();
        })
        .then(response => {

            console.log('Anggota response:', response);

            let data = [];

            if(Array.isArray(response)){
                data = response;
            }
            else if(response.data && Array.isArray(response.data)){
                data = response.data;
            }

            if(data.length === 0){
                alert('Anggota tidak ditemukan');
                return;
            }

            let html = `
                <div class="p-2 font-semibold bg-gray-100">
                    Hasil Pencarian
                </div>
            `;

            data.forEach(anggota => {

                html += `
                <div class="p-3 hover:bg-indigo-50 cursor-pointer border-b anggota-item"
                    data-id="${anggota.id}"
                    data-name="${anggota.name}"
                    data-nisn="${anggota.nisn_nik ?? '-'}"
                    data-role="${anggota.role ?? '-'}">

                    <div class="font-medium">
                        ${anggota.name}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        ${anggota.role ?? '-'} |
                        ${anggota.nisn_nik ?? '-'}
                    </div>

                </div>
                `;
            });

            anggotaResults.innerHTML=html;
            anggotaResults.classList.remove('hidden');

            document.querySelectorAll('.anggota-item').forEach(item=>{
                item.addEventListener('click',function(){

                    let id=this.dataset.id;
                    let name=this.dataset.name;
                    let role=this.dataset.role;
                    let nisn=this.dataset.nisn;

                    searchAnggota.value=
                        `${name} (${role}) - ${nisn}`;

                    searchAnggota.readOnly=true;
                    searchAnggota.classList.add(
                        'bg-gray-100',
                        'cursor-not-allowed'
                    );

                    userIdInput.value=id;

                    selectedAnggotaNama.innerText=
                        `${name} (${role})`;

                    selectedAnggotaInfo.classList.remove('hidden');

                    anggotaResults.classList.add('hidden');

                    btnCariAnggota.style.display='none';
                    btnResetAnggota.classList.remove('hidden');
                });
            });

        })
        .catch(error=>{
            console.error(error);
            alert('Gagal memuat data anggota');
        });
    }

    if (searchAnggota) {
        searchAnggota.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                cariAnggota();
            }
        });
    }
    if (btnCariAnggota) btnCariAnggota.addEventListener('click', cariAnggota);
    if (btnResetAnggota) btnResetAnggota.addEventListener('click', resetAnggota);

    // ============================================
    // BUKU
    // ============================================
    const searchBuku = document.getElementById('searchBuku');
    const bukuResults = document.getElementById('bukuResults');
    const bukuIdInput = document.getElementById('buku_id');
    const btnCariBuku = document.getElementById('btnCariBuku');
    const btnResetBuku = document.getElementById('btnResetBuku');
    const selectedBukuInfo = document.getElementById('selectedBukuInfo');
    const selectedBukuJudul = document.getElementById('selectedBukuJudul');

    function resetBuku() {
        searchBuku.value = '';
        searchBuku.readOnly = false;
        searchBuku.classList.remove('bg-gray-100', 'cursor-not-allowed');
        bukuIdInput.value = '';
        selectedBukuInfo.classList.add('hidden');
        btnCariBuku.style.display = 'flex';
        btnResetBuku.classList.add('hidden');
        searchBuku.focus();
    }

    function cariBuku(){

        const query=searchBuku.value.trim();

        if(query.length<2){
            alert('Minimal 2 karakter');
            return;
        }

        fetch(`{{ route('petugas.sirkulasi.cari-buku') }}?q=${encodeURIComponent(query)}`,{
            headers:{
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest'
            }
        })
        .then(async response=>{

            if(!response.ok){
                throw new Error('Response gagal');
            }

            return response.json();
        })
        .then(response=>{

            console.log('Buku response:',response);

            let data=[];

            if(Array.isArray(response)){
                data=response;
            }
            else if(response.data && Array.isArray(response.data)){
                data=response.data;
            }

            if(data.length===0){
                alert('Buku tidak ditemukan');
                return;
            }

            let html=`
            <div class="p-2 font-semibold bg-gray-100">
                Hasil Pencarian
            </div>
            `;

            data.forEach(buku=>{

                html+=`
                <div class="p-3 hover:bg-indigo-50 cursor-pointer border-b buku-item"
                    data-id="${buku.id}"
                    data-judul="${buku.judul}"
                    data-pengarang="${buku.pengarang ?? '-'}"
                    data-penerbit="${buku.penerbit ?? '-'}"
                    data-tahun="${buku.tahun_terbit ?? '-'}"
                    data-stok="${buku.stok_tersedia ?? 0}">

                    <div class="font-medium">
                        ${buku.judul}
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        ${buku.pengarang ?? '-'}
                        |
                        Stok:
                        ${buku.stok_tersedia ?? 0}
                    </div>

                </div>
                `;
            });

            bukuResults.innerHTML=html;
            bukuResults.classList.remove('hidden');

            document.querySelectorAll('.buku-item').forEach(item=>{
                item.addEventListener('click',function(){

                    let id=this.dataset.id;
                    let judul=this.dataset.judul;
                    let pengarang=this.dataset.pengarang;
                    let stok=this.dataset.stok;

                    searchBuku.value=
                        `${judul} - ${pengarang} (Stok:${stok})`;

                    searchBuku.readOnly=true;
                    searchBuku.classList.add(
                        'bg-gray-100',
                        'cursor-not-allowed'
                    );

                    bukuIdInput.value=id;

                    selectedBukuJudul.innerText=
                        `${judul} - ${pengarang}`;

                    selectedBukuInfo.classList.remove('hidden');

                    bukuResults.classList.add('hidden');

                    btnCariBuku.style.display='none';
                    btnResetBuku.classList.remove('hidden');

                });
            });

        })
        .catch(error=>{
            console.error(error);
            alert('Gagal memuat data buku');
        });

    }

    if (searchBuku) {
        searchBuku.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                cariBuku();
            }
        });
    }
    if (btnCariBuku) btnCariBuku.addEventListener('click', cariBuku);
    if (btnResetBuku) btnResetBuku.addEventListener('click', resetBuku);

    // Sembunyikan hasil saat klik di luar
    document.addEventListener('click', function(e) {
        if (anggotaResults && !searchAnggota.contains(e.target) && !anggotaResults.contains(e.target)) {
            anggotaResults.classList.add('hidden');
        }
        if (bukuResults && !searchBuku.contains(e.target) && !bukuResults.contains(e.target)) {
            bukuResults.classList.add('hidden');
        }
    });
});

// ============================================
// FUNGSI GLOBAL
// ============================================
function verifikasiData() {
    const userId = document.getElementById('user_id').value;
    const bukuId = document.getElementById('buku_id').value;
    const kodeInput = document.getElementById('kodeEksemplar');
    const tglPinjam = document.getElementById('tanggalPinjam');
    const jatuhTempo = document.getElementById('jatuhTempo');
    
    if (!userId) {
        alert('Silakan cari dan pilih anggota terlebih dahulu!');
        document.getElementById('searchAnggota').focus();
        return;
    }
    if (!bukuId) {
        alert('Silakan cari dan pilih buku terlebih dahulu!');
        document.getElementById('searchBuku').focus();
        return;
    }
    if (!kodeInput.value.trim()) {
        alert('Kode eksemplar harus diisi!');
        kodeInput.focus();
        return;
    }
    
    document.getElementById('verifAnggota').innerText = document.getElementById('searchAnggota').value || '-';
    document.getElementById('verifBuku').innerText = document.getElementById('searchBuku').value || '-';
    document.getElementById('verifKode').innerText = kodeInput.value.trim();
    
    const tglPinjamDate = new Date(tglPinjam.value);
    const jatuhTempoDate = new Date(jatuhTempo.value);
    
    document.getElementById('verifTglPinjam').innerText = tglPinjamDate.toLocaleDateString('id-ID');
    document.getElementById('verifJatuhTempo').innerText = jatuhTempoDate.toLocaleDateString('id-ID');
    
    document.getElementById('verifikasiModal').style.display = 'flex';
}

function tutupModal() {
    const modal = document.getElementById('verifikasiModal');
    modal.style.display = 'none';
}

function submitForm() {
    const jatuhTempo = document.getElementById('jatuhTempo');
    if (jatuhTempo) jatuhTempo.disabled = false;
    document.getElementById('formPeminjaman').submit();
}

window.onclick = function(event) {
    const modal = document.getElementById('verifikasiModal');
    if (event.target === modal) tutupModal();
}
</script>
@endpush