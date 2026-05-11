@extends('petugas.layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="p-4 md:p-6 max-w-6xl mx-auto">
    
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.buku.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">✏️ Edit Buku</h1>
                <p class="text-sm text-gray-500 mt-1">Edit informasi buku: <span class="font-medium text-indigo-600">{{ $buku->judul }}</span></p>
            </div>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between max-w-5xl mx-auto">
            <div class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">1</div>
                <span class="font-medium text-indigo-600 hidden sm:inline">Data Eksemplar</span>
            </div>
            <div class="flex-1 h-0.5 bg-indigo-200 mx-2"></div>
            <div class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold">2</div>
                <span class="text-gray-500 hidden sm:inline">Data Bibliografis</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold">3</div>
                <span class="text-gray-500 hidden sm:inline">File Digital</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
            <div class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-semibold">4</div>
                <span class="text-gray-500 hidden sm:inline">Upload Cover</span>
            </div>
        </div>
    </div>

    {{-- Form Edit Buku --}}
    <form action="{{ route('petugas.buku.update', $buku->id) }}" 
          method="POST" 
          enctype="multipart/form-data"
          id="formBuku"
          class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-8">
            
            {{-- ============================================================ --}}
            {{-- SECTION 1: DATA EKSEMPLAR --}}
            {{-- ============================================================ --}}
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    A. Data Eksemplar
                </h2>
                <p class="text-xs text-gray-500 mt-1">Informasi fisik dan administratif eksemplar buku</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- NO. INDUK (Auto-generated - Readonly) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No. Induk 
                            <span class="text-xs text-gray-400">(Otomatis)</span>
                        </label>
                        <input type="text" 
                               value="{{ $buku->id }}"
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed"
                               readonly
                               disabled>
                        <p class="text-xs text-gray-400 mt-1">ID unik sistem (tidak dapat diubah)</p>
                    </div>

                    {{-- NO. BARCODE --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No. Barcode 
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="barcode" 
                               id="barcodeInputField"
                               value="{{ old('barcode', $buku->barcode) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 @error('barcode') border-red-500 @enderror"
                               placeholder="Kode barcode">
                        @error('barcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Kode barcode unik untuk eksemplar ini</p>
                        @enderror
                    </div>

                    {{-- TIPE BUKU --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe Buku <span class="text-red-500">*</span>
                        </label>
                        <select name="tipe" id="tipeBuku" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="fisik" {{ old('tipe', $buku->tipe) == 'fisik' ? 'selected' : '' }}>📖 Buku Fisik</option>
                            <option value="digital" {{ old('tipe', $buku->tipe) == 'digital' ? 'selected' : '' }}>💻 Buku Digital (E-book)</option>
                        </select>
                    </div>

                    {{-- NO. RFID --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No. RFID 
                            <span class="text-xs text-gray-400">(Opsional)</span>
                        </label>
                        <input type="text" 
                               name="rfid" 
                               value="{{ old('rfid', $buku->rfid) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Kode RFID jika tersedia">
                    </div>

                    {{-- JUMLAH EKSEMPLAR --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Eksemplar <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="stok" 
                               value="{{ old('stok', $buku->stok) }}"
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 @error('stok') border-red-500 @enderror"
                               required>
                        <p class="text-xs text-gray-400 mt-1">Jumlah eksemplar (No. Induk, Barcode, RFID akan digenerate otomatis)</p>
                    </div>

                    {{-- JENIS SUMBER --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Sumber 
                        </label>
                        <select name="sumber_jenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="pembelian" {{ old('sumber_jenis', $buku->sumber_jenis) == 'pembelian' ? 'selected' : '' }}>💰 Pembelian</option>
                            <option value="hadiah_hibah" {{ old('sumber_jenis', $buku->sumber_jenis) == 'hadiah_hibah' ? 'selected' : '' }}>🎁 Hadiah/Hibah</option>
                            <option value="penggantian" {{ old('sumber_jenis', $buku->sumber_jenis) == 'penggantian' ? 'selected' : '' }}>🔄 Penggantian</option>
                            <option value="penggandaan" {{ old('sumber_jenis', $buku->sumber_jenis) == 'penggandaan' ? 'selected' : '' }}>📋 Penggandaan</option>
                            <option value="tukar_menukar" {{ old('sumber_jenis', $buku->sumber_jenis) == 'tukar_menukar' ? 'selected' : '' }}>🔄 Tukar Menukar</option>
                            <option value="terbitan_sendiri" {{ old('sumber_jenis', $buku->sumber_jenis) == 'terbitan_sendiri' ? 'selected' : '' }}>📝 Terbitan Sendiri</option>
                            <option value="deposit" {{ old('sumber_jenis', $buku->sumber_jenis) == 'deposit' ? 'selected' : '' }}>📦 Deposit (UU No.4/1990)</option>
                        </select>
                    </div>

                    {{-- NAMA SUMBER --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Sumber
                        </label>
                        <input type="text" 
                               name="sumber_nama" 
                               value="{{ old('sumber_nama', $buku->sumber_nama) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Nama toko/pemberi/donatur">
                    </div>

                    {{-- TANGGAL PENGADAAN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Pengadaan
                        </label>
                        <input type="date" 
                               name="tanggal_pengadaan" 
                               value="{{ old('tanggal_pengadaan', $buku->tanggal_pengadaan ? \Carbon\Carbon::parse($buku->tanggal_pengadaan)->format('Y-m-d') : date('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                        <p class="text-xs text-gray-400 mt-1">Tanggal buku diterima/dibeli</p>
                    </div>

                    {{-- HARGA --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Harga
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" 
                                   name="harga" 
                                   value="{{ old('harga', $buku->harga) }}"
                                   min="0"
                                   step="1000"
                                   class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                        </div>
                    </div>

                    {{-- KATEGORI KOLEKSI --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori Koleksi <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_koleksi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200" required>
                            <option value="">-- Pilih Kategori Koleksi --</option>
                            <option value="buku_paket" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'buku_paket' ? 'selected' : '' }}>📚 Buku Paket</option>
                            <option value="fisik" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'fisik' ? 'selected' : '' }}>📖 Koleksi Fisik</option>
                            <option value="referensi" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'referensi' ? 'selected' : '' }}>📕 Koleksi Referensi</option>
                            <option value="non_fiksi" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'non_fiksi' ? 'selected' : '' }}>📗 Koleksi Non Fiksi</option>
                            <option value="umum" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'umum' ? 'selected' : '' }}>📘 Koleksi Umum</option>
                            <option value="paket" {{ old('kategori_koleksi', $buku->kategori_koleksi) == 'paket' ? 'selected' : '' }}>📙 Koleksi Paket</option>
                        </select>
                    </div>

                    {{-- KATEGORI BUKU --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori Buku <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 @error('kategori_id') border-red-500 @enderror"
                                required>
                            <option value="">-- Pilih Kategori Buku --</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id', $buku->kategori_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- LOKASI (TETAP) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lokasi
                        </label>
                        <input type="text" 
                               name="lokasi" 
                               value="Ruang Baca Umum Perpustakaan Tambang Ilmu"
                               class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed"
                               readonly>
                        <p class="text-xs text-gray-400 mt-1">Lokasi tetap: Ruang Baca Umum Perpustakaan Tambang Ilmu</p>
                    </div>

                    {{-- LOKASI RAK --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Lokasi Rak
                        </label>
                        <input type="text" 
                               name="rak" 
                               value="{{ old('rak', $buku->rak) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: R-A1">
                    </div>

                    {{-- MEDIA / FORMAT --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Media / Format
                        </label>
                        <select name="format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="Cetak" {{ old('format', $buku->format) == 'Cetak' ? 'selected' : '' }}>📖 Cetak (Fisik)</option>
                            <option value="PDF" {{ old('format', $buku->format) == 'PDF' ? 'selected' : '' }}>📄 PDF</option>
                            <option value="EPUB" {{ old('format', $buku->format) == 'EPUB' ? 'selected' : '' }}>📱 EPUB</option>
                        </select>
                    </div>

                    {{-- DENDA PER HARI (OTOMATIS 500) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Denda per Hari
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number" 
                                   name="denda_per_hari" 
                                   value="500"
                                   readonly
                                   class="w-full pl-12 pr-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Denda tetap Rp 500 per hari (tidak dapat diubah)</p>
                    </div>

                    {{-- KETERSEDIAAN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Status Ketersediaan
                        </label>
                        <select name="ketersediaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="tersedia" {{ old('ketersediaan', $buku->ketersediaan) == 'tersedia' ? 'selected' : '' }}>✅ Tersedia</option>
                            <option value="dipinjam" {{ old('ketersediaan', $buku->ketersediaan) == 'dipinjam' ? 'selected' : '' }}>📤 Dipinjam</option>
                            <option value="rusak" {{ old('ketersediaan', $buku->ketersediaan) == 'rusak' ? 'selected' : '' }}>⚠️ Rusak</option>
                            <option value="hilang" {{ old('ketersediaan', $buku->ketersediaan) == 'hilang' ? 'selected' : '' }}>❌ Hilang</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- SECTION 2: DATA BIBLIOGRAFIS --}}
            {{-- ============================================================ --}}
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    B. Data Bibliografis
                </h2>
                <p class="text-xs text-gray-500 mt-1">Informasi identitas dan isi buku</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- JUDUL UTAMA --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Judul Utama <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="judul" 
                               value="{{ old('judul', $buku->judul) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 @error('judul') border-red-500 @enderror"
                               placeholder="Masukkan judul lengkap buku"
                               required>
                    </div>

                    {{-- ANAK JUDUL --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Anak Judul (Sub Judul)
                        </label>
                        <input type="text" 
                               name="sub_judul" 
                               value="{{ old('sub_judul', $buku->sub_judul) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Sub judul atau judul tambahan">
                    </div>

                    {{-- PENGARANG --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pengarang
                        </label>
                        <input type="text" 
                               name="pengarang" 
                               value="{{ old('pengarang', $buku->pengarang) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Nama pengarang utama">
                    </div>

                    {{-- PENGARANG TAMBAHAN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pengarang Tambahan
                        </label>
                        <input type="text" 
                               name="pengarang_tambahan" 
                               value="{{ old('pengarang_tambahan', $buku->pengarang_tambahan) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Pisah dengan koma">
                    </div>

                    {{-- EDISI --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Edisi
                        </label>
                        <input type="text" 
                               name="edisi" 
                               value="{{ old('edisi', $buku->edisi) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: Edisi Revisi, Cetakan ke-3">
                    </div>

                    {{-- KOTA TERBIT --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kota Terbit
                        </label>
                        <input type="text" 
                               name="kota_terbit" 
                               value="{{ old('kota_terbit', $buku->kota_terbit) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: Jakarta">
                    </div>

                    {{-- PENERBIT --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Penerbit
                        </label>
                        <input type="text" 
                               name="penerbit" 
                               value="{{ old('penerbit', $buku->penerbit) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Nama penerbit">
                    </div>

                    {{-- TAHUN TERBIT --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun Terbit
                        </label>
                        <input type="number" 
                               name="tahun_terbit" 
                               value="{{ old('tahun_terbit', $buku->tahun_terbit) }}"
                               min="1000" 
                               max="{{ date('Y') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: 2024">
                    </div>

                    {{-- JUMLAH HALAMAN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jumlah Halaman
                        </label>
                        <input type="number" 
                               name="jumlah_halaman" 
                               value="{{ old('jumlah_halaman', $buku->jumlah_halaman) }}"
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: 250">
                    </div>

                    {{-- DIMENSI / UKURAN - VERSION 2 KOLOM (EDIT) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Dimensi / Ukuran <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Dropdown Pilihan Standar --}}
                            <div>
                                <select id="ukuranSelect" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                                    <option value="">-- Pilih Ukuran Standar --</option>
                                    <option value="A4 (21 x 29,7 cm)" {{ old('ukuran', $buku->ukuran) == 'A4 (21 x 29,7 cm)' ? 'selected' : '' }}>📄 A4 (21 x 29,7 cm)</option>
                                    <option value="B5 (17,6 x 25 cm)" {{ old('ukuran', $buku->ukuran) == 'B5 (17,6 x 25 cm)' ? 'selected' : '' }}>📘 B5 (17,6 x 25 cm)</option>
                                    <option value="A5 (14,8 x 21 cm)" {{ old('ukuran', $buku->ukuran) == 'A5 (14,8 x 21 cm)' ? 'selected' : '' }}>📖 A5 (14,8 x 21 cm)</option>
                                    <option value="UNESCO (15,5 x 23 cm)" {{ old('ukuran', $buku->ukuran) == 'UNESCO (15,5 x 23 cm)' ? 'selected' : '' }}>📚 UNESCO (15,5 x 23 cm)</option>
                                    <option value="F4 (21 x 33 cm)" {{ old('ukuran', $buku->ukuran) == 'F4 (21 x 33 cm)' ? 'selected' : '' }}>📋 F4 (21 x 33 cm)</option>
                                    <option value="Pocket (13 x 19 cm)" {{ old('ukuran', $buku->ukuran) == 'Pocket (13 x 19 cm)' ? 'selected' : '' }}>👝 Pocket (13 x 19 cm)</option>
                                    <option value="Pocket (13 x 20 cm)" {{ old('ukuran', $buku->ukuran) == 'Pocket (13 x 20 cm)' ? 'selected' : '' }}>👝 Pocket (13 x 20 cm)</option>
                                    <option value="custom" {{ old('ukuran', $buku->ukuran) && !in_array(old('ukuran', $buku->ukuran), ['', 'A4 (21 x 29,7 cm)', 'B5 (17,6 x 25 cm)', 'A5 (14,8 x 21 cm)', 'UNESCO (15,5 x 23 cm)', 'F4 (21 x 33 cm)', 'Pocket (13 x 19 cm)', 'Pocket (13 x 20 cm)']) ? 'selected' : '' }}>✏️ Custom - Isi sendiri</option>
                                </select>
                            </div>
                            
                            {{-- Kolom Custom Input (Awalnya hidden) --}}
                            <div id="customUkuranContainer" class="hidden md:col-span-2">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Panjang (cm)</label>
                                        <input type="number" 
                                            id="panjangInput"
                                            step="0.1"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                                            placeholder="Contoh: 21">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Lebar (cm)</label>
                                        <input type="number" 
                                            id="lebarInput"
                                            step="0.1"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                                            placeholder="Contoh: 29,7">
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">⚠️ Gunakan titik untuk desimal (contoh: 29.7)</p>
                            </div>
                        </div>
                        
                        {{-- Hidden Input untuk nilai akhir --}}
                        <input type="hidden" name="ukuran" id="ukuranHidden" value="{{ old('ukuran', $buku->ukuran) }}">
                        
                        {{-- Preview Ukuran --}}
                        <div id="ukuranPreview" class="mt-2 text-sm text-indigo-600 font-medium {{ old('ukuran', $buku->ukuran) ? '' : 'hidden' }}">
                            📏 Ukuran yang dipilih: <span id="selectedUkuran">{{ old('ukuran', $buku->ukuran) }}</span>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-2">
                            💡 <span class="font-medium">Petunjuk:</span> Pilih ukuran standar dari dropdown, atau pilih "Custom" untuk memasukkan ukuran sendiri (panjang x lebar dalam cm)
                        </p>
                        @error('ukuran')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ISBN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ISBN
                        </label>
                        <input type="text" 
                               name="isbn" 
                               id="isbn"
                               value="{{ old('isbn', $buku->isbn) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="978-XXX-XXXXX-XX-X">
                    </div>

                    {{-- ISSN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ISSN
                        </label>
                        <input type="text" 
                               name="issn" 
                               value="{{ old('issn', $buku->issn) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="XXXX-XXXX">
                    </div>

                    {{-- NO DDC --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No. DDC
                        </label>
                        <input type="text" 
                               name="no_ddc" 
                               value="{{ old('no_ddc', $buku->no_ddc) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: 823">
                    </div>

                    {{-- NOMOR PANGGIL --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Panggil
                        </label>
                        <input type="text" 
                               name="nomor_panggil" 
                               value="{{ old('nomor_panggil', $buku->nomor_panggil) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Contoh: 823 PRA m">
                    </div>

                    {{-- BAHASA --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bahasa
                        </label>
                        <select name="bahasa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="Indonesia" {{ old('bahasa', $buku->bahasa) == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                            <option value="Inggris" {{ old('bahasa', $buku->bahasa) == 'Inggris' ? 'selected' : '' }}>Inggris</option>
                            <option value="Arab" {{ old('bahasa', $buku->bahasa) == 'Arab' ? 'selected' : '' }}>Arab</option>
                            <option value="Mandarin" {{ old('bahasa', $buku->bahasa) == 'Mandarin' ? 'selected' : '' }}>Mandarin</option>
                        </select>
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Deskripsi / Sinopsis
                        </label>
                        <textarea name="deskripsi" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                                  placeholder="Sinopsis atau ringkasan isi buku...">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                    </div>

                    {{-- KATA KUNCI --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kata Kunci (Subjek Topik)
                        </label>
                        <input type="text" 
                               name="kata_kunci" 
                               value="{{ old('kata_kunci', $buku->kata_kunci) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200"
                               placeholder="Pisahkan dengan koma, contoh: fiksi, petualangan, pendidikan">
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- SECTION 3: FILE DIGITAL (E-BOOK) --}}
            {{-- ============================================================ --}}
            <div id="digitalSection" class="space-y-4 {{ old('tipe', $buku->tipe) == 'digital' ? '' : 'hidden' }}">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    C. File Digital (E-book)
                </h2>
                <p class="text-xs text-gray-500 mt-1">Upload file e-book untuk koleksi digital</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Upload File E-book --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            File E-book <span class="text-red-500" id="fileRequired">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-500 transition-colors cursor-pointer"
                             onclick="document.getElementById('file_ebook').click()">
                            <input type="file" 
                                   id="file_ebook" 
                                   name="file_path" 
                                   accept=".pdf,.epub"
                                   class="hidden"
                                   onchange="updateFileName(this)">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mt-1 text-sm text-gray-600" id="file_name">
                                @if($buku->file_path)
                                    File saat ini: {{ basename($buku->file_path) }}
                                @else
                                    Klik untuk upload file
                                @endif
                            </p>
                            <p class="text-xs text-gray-500">Format: PDF, EPUB (max 20MB)</p>
                        </div>
                    </div>

                    {{-- Jenis Koleksi Digital --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenis Koleksi Digital
                        </label>
                        <select name="jenis_koleksi" id="jenisKoleksi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                            <option value="ebook" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'ebook' ? 'selected' : '' }}>📚 E-Book (Perlu Pinjam)</option>
                            <option value="soal" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'soal' ? 'selected' : '' }}>📝 Bank Soal (Download Bebas)</option>
                            <option value="modul" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'modul' ? 'selected' : '' }}>📖 Modul (Download Bebas)</option>
                            <option value="dokumen" {{ old('jenis_koleksi', $buku->jenis_koleksi) == 'dokumen' ? 'selected' : '' }}>📄 Dokumen (Download Bebas)</option>
                        </select>
                    </div>

                    {{-- Lisensi (hanya untuk E-Book) --}}
                    <div id="lisensiSection" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Durasi Pinjam (Hari)
                            </label>
                            <input type="number" 
                                   name="durasi_pinjam_hari" 
                                   id="durasi_pinjam"
                                   value="7"
                                   readonly
                                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">E-book: 7 hari (tidak dapat diubah)</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Level Akses
                            </label>
                            <select name="access_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-200">
                                <option value="public" {{ old('access_level', $buku->access_level) == 'public' ? 'selected' : '' }}>🌐 Public (Semua orang)</option>
                                <option value="member_only" {{ old('access_level', $buku->access_level) == 'member_only' ? 'selected' : '' }}>🔒 Anggota (Hanya anggota terdaftar)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- SECTION 4: UPLOAD COVER --}}
            {{-- ============================================================ --}}
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    D. Upload Sampul Buku
                </h2>

                <div class="flex items-center gap-6">
                    <div class="flex-1">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition-colors cursor-pointer"
                             onclick="document.getElementById('sampul').click()">
                            <input type="file" 
                                   id="sampul" 
                                   name="sampul" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif"
                                   class="hidden"
                                   onchange="previewImage(this)">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-600">Klik untuk ganti sampul buku</p>
                            <p class="text-xs text-gray-500">Format: JPG, PNG, GIF (max 2MB)</p>
                        </div>
                    </div>
                    <div id="previewContainer" class="w-32 h-40 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                        @if($buku->sampul && Storage::disk('public')->exists($buku->sampul))
                            <img id="imagePreview" src="{{ asset('storage/'.$buku->sampul) }}" alt="Preview" class="w-full h-full object-cover">
                        @else
                            <img id="imagePreview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                            <div id="noImagePlaceholder" class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
                @if($buku->sampul)
                    <p class="text-xs text-gray-500">File saat ini: {{ basename($buku->sampul) }}</p>
                @endif
            </div>

            {{-- Tombol Submit --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('petugas.buku.index') }}" 
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Update Buku
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Preview image cover
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('previewContainer');
    const placeholder = document.getElementById('noImagePlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            container.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Update file name for ebook
function updateFileName(input) {
    const fileName = document.getElementById('file_name');
    if (input.files && input.files[0]) {
        fileName.textContent = input.files[0].name;
    } else {
        fileName.textContent = 'Klik untuk upload file';
    }
}

// Toggle Digital Section
const tipeBuku = document.getElementById('tipeBuku');
const digitalSection = document.getElementById('digitalSection');
const digitalContent = document.getElementById('digitalContent');
const fileRequired = document.getElementById('fileRequired');

function toggleDigitalSection() {
    if (tipeBuku.value === 'digital') {
        digitalSection.classList.remove('hidden');
        if (fileRequired) fileRequired.style.display = 'inline';
    } else {
        digitalSection.classList.add('hidden');
        if (fileRequired) fileRequired.style.display = 'none';
    }
}

tipeBuku.addEventListener('change', toggleDigitalSection);
toggleDigitalSection();

// Toggle Lisensi Section
const jenisKoleksi = document.getElementById('jenisKoleksi');
const lisensiSection = document.getElementById('lisensiSection');

function toggleLisensiSection() {
    if (jenisKoleksi && jenisKoleksi.value === 'ebook') {
        lisensiSection.style.display = 'grid';
    } else if (lisensiSection) {
        lisensiSection.style.display = 'none';
    }
}

if (jenisKoleksi) {
    jenisKoleksi.addEventListener('change', toggleLisensiSection);
    toggleLisensiSection();
}

// Validasi ISBN format
document.getElementById('isbn')?.addEventListener('blur', function() {
    let isbn = this.value.replace(/[-\s]/g, '');
    if (isbn && !/^(\d{10}|\d{13})$/.test(isbn)) {
        this.style.borderColor = '#ef4444';
        this.style.backgroundColor = '#fef2f2';
    } else {
        this.style.borderColor = '';
        this.style.backgroundColor = '';
    }
});

// Form submission loading
document.getElementById('formBuku')?.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Menyimpan...
    `;
});
</script>
@endpush

@push('styles')
<style>
input:focus, select:focus, textarea:focus {
    outline: none;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endpush
