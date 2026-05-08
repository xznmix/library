@extends('petugas.layouts.app')

@section('title', 'Edit Koleksi Digital')

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.koleksi-digital.show', $buku->id) }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Koleksi Digital</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi: <span class="font-medium text-indigo-600">{{ $buku->judul }}</span></p>
            </div>
        </div>
    </div>

    {{-- Form Edit --}}
    <form action="{{ route('petugas.koleksi-digital.update', $buku->id) }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            
            {{-- Informasi Dasar --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Dasar
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Judul --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               value="{{ old('judul', $buku->judul) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all @error('judul') border-red-500 @enderror"
                               required>
                        @error('judul')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Koleksi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Koleksi <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_koleksi" 
                                id="jenisKoleksi"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 @error('jenis_koleksi') border-red-500 @enderror"
                                required>
                            <option value="ebook" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'ebook' ? 'selected' : '' }}>📚 E-Book (Pinjam - dengan lisensi)</option>
                            <option value="soal" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'soal' ? 'selected' : '' }}>📝 Bank Soal (Download bebas)</option>
                            <option value="modul" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'modul' ? 'selected' : '' }}>📖 Modul Pembelajaran (Download bebas)</option>
                            <option value="dokumen" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'dokumen' ? 'selected' : '' }}>📄 Dokumen (Download bebas)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1" id="jenisKoleksiHint">
                            @if($buku->jenis_koleksi == 'ebook')
                                📚 E-Book memerlukan pinjam dan memiliki batasan lisensi.
                            @else
                                ⬇️ Koleksi ini dapat langsung di-download tanpa pinjam.
                            @endif
                        </p>
                        @error('jenis_koleksi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pengarang --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
                        <input type="text" 
                               name="pengarang" 
                               value="{{ old('pengarang', $buku->pengarang) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- Penerbit --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                        <input type="text" 
                               name="penerbit" 
                               value="{{ old('penerbit', $buku->penerbit) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- Tahun Terbit --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                        <input type="number" 
                               name="tahun_terbit" 
                               value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                               min="1900" 
                               max="{{ date('Y') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- ISBN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                        <input type="text" 
                               name="isbn" 
                               value="{{ old('isbn', $buku->isbn) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 @error('kategori_id') border-red-500 @enderror"
                                required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id', $buku->kategori_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                </div>
            </div>

            {{-- File Upload --}}
            <div class="border-t pt-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload File (Kosongkan jika tidak ingin mengubah)
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- File --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-500 transition-colors cursor-pointer"
                             onclick="document.getElementById('file_ebook').click()">
                            <input type="file" 
                                   id="file_ebook" 
                                   name="file_ebook" 
                                   accept=".pdf,.epub"
                                   class="hidden"
                                   onchange="updateFileName(this)">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-600" id="file_name">
                                File saat ini: {{ basename($buku->file_path) }}
                            </p>
                            <p class="text-xs text-gray-500">Format: PDF, EPUB (max 20MB)</p>
                        </div>
                    </div>

                    {{-- Cover --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-500 transition-colors cursor-pointer"
                             onclick="document.getElementById('cover').click()">
                            <input type="file" 
                                   id="cover" 
                                   name="cover" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewCover(this)">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">Klik untuk ganti cover</p>
                            <p class="text-xs text-gray-500">Format: JPG, PNG (max 2MB)</p>
                        </div>
                        <div id="coverPreview" class="mt-2 {{ $buku->cover_path ? '' : 'hidden' }}">
                            <img src="{{ $buku->cover_path ? asset('storage/'.$buku->cover_path) : '#' }}" 
                                 alt="Preview Cover" 
                                 class="h-20 rounded-lg">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manajemen Lisensi --}}
            <div id="lisensiSection" class="border-t pt-4">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Manajemen Lisensi
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Jumlah Lisensi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Lisensi
                        </label>
                        <input type="number" 
                               name="jumlah_lisensi" 
                               id="jumlah_lisensi"
                               value="{{ old('jumlah_lisensi', $buku->jumlah_lisensi) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                        <p class="text-xs text-gray-500 mt-1">Jumlah yang bisa dipinjam bersamaan</p>
                    </div>

                    {{-- Durasi Pinjam --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Durasi Pinjam (Jam)
                        </label>
                        <input type="number" 
                               name="durasi_pinjam_hari" 
                               id="durasi_pinjam"
                               value="{{ old('durasi_pinjam_hari', $buku->durasi_pinjam_hari) }}"
                               min="1" 
                               max="168"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                        <p class="text-xs text-gray-500 mt-1">Default 24 jam (1 hari)</p>
                    </div>

                    {{-- Akses Digital --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe Akses
                        </label>
                        <select name="akses_digital" id="akses_digital" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="online_only" {{ old('akses_digital', $buku->akses_digital) == 'online_only' ? 'selected' : '' }}>Online Only (Baca di web)</option>
                            <option value="download_terbatas" {{ old('akses_digital', $buku->akses_digital) == 'download_terbatas' ? 'selected' : '' }}>Download Terbatas</option>
                            <option value="full_access" {{ old('akses_digital', $buku->akses_digital) == 'full_access' ? 'selected' : '' }}>Full Access</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    {{-- Penerbit Lisensi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit Lisensi</label>
                        <input type="text" 
                               name="penerbit_lisensi" 
                               value="{{ old('penerbit_lisensi', $buku->penerbit_lisensi) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: Gramedia, Erlangga">
                    </div>

                    {{-- Tanggal Berlaku --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berlaku Lisensi</label>
                        <input type="date" 
                               name="tanggal_berlaku_lisensi" 
                               value="{{ old('tanggal_berlaku_lisensi', $buku->tanggal_berlaku_lisensi ? \Carbon\Carbon::parse($buku->tanggal_berlaku_lisensi)->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- Tanggal Kadaluarsa --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa Lisensi</label>
                        <input type="date" 
                               name="tanggal_kadaluarsa_lisensi" 
                               value="{{ old('tanggal_kadaluarsa_lisensi', $buku->tanggal_kadaluarsa_lisensi ? \Carbon\Carbon::parse($buku->tanggal_kadaluarsa_lisensi)->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    </div>

                    {{-- Catatan Lisensi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Lisensi</label>
                        <textarea name="catatan_lisensi" 
                                  rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                                  placeholder="Informasi tambahan tentang lisensi...">{{ old('catatan_lisensi', $buku->catatan_lisensi) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('petugas.koleksi-digital.show', $buku->id) }}" 
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Update Koleksi Digital
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function updateFileName(input) {
    const fileName = document.getElementById('file_name');
    if (input.files.length > 0) {
        fileName.textContent = 'File baru: ' + input.files[0].name;
    } else {
        fileName.textContent = 'File saat ini: {{ basename($buku->file_path) }}';
    }
}

function previewCover(input) {
    const preview = document.getElementById('coverPreview');
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle lisensi section berdasarkan jenis koleksi
const jenisKoleksi = document.getElementById('jenisKoleksi');
const lisensiSection = document.getElementById('lisensiSection');
const hintText = document.getElementById('jenisKoleksiHint');
const jumlahLisensi = document.querySelector('input[name="jumlah_lisensi"]');
const durasiPinjam = document.querySelector('input[name="durasi_pinjam_hari"]');

if (jenisKoleksi) {
    function toggleLisensiSection() {
        const isEbook = jenisKoleksi.value === 'ebook';
        
        if (isEbook) {
            lisensiSection.style.display = 'block';
            hintText.innerHTML = '📚 E-Book memerlukan pinjam dan memiliki batasan lisensi.';
            if (jumlahLisensi) {
                jumlahLisensi.required = true;
                if (!jumlahLisensi.value) jumlahLisensi.value = 3;
            }
            if (durasiPinjam) {
                durasiPinjam.required = true;
                if (!durasiPinjam.value) durasiPinjam.value = 24;
            }
        } else {
            lisensiSection.style.display = 'none';
            const selectedText = jenisKoleksi.options[jenisKoleksi.selectedIndex].text;
            hintText.innerHTML = '⬇️ ' + selectedText + ' dapat langsung di-download tanpa pinjam.';
            if (jumlahLisensi) jumlahLisensi.required = false;
            if (durasiPinjam) durasiPinjam.required = false;
        }
    }
    
    jenisKoleksi.addEventListener('change', toggleLisensiSection);
    
    // Set initial state berdasarkan nilai saat ini
    const currentJenis = '{{ old('jenis_koleksi', $buku->jenis_koleksi) }}';
    if (currentJenis !== 'ebook') {
        lisensiSection.style.display = 'none';
        if (jumlahLisensi) jumlahLisensi.required = false;
        if (durasiPinjam) durasiPinjam.required = false;
    }
}
</script>
@endpush