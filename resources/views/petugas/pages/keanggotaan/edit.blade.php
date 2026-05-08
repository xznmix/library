@extends('petugas.layouts.app')

@section('title', 'Edit Data Anggota')

@section('content')
<div class="p-4 md:p-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.keanggotaan.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Data Anggota</h1>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi anggota</p>
            </div>
        </div>
    </div>

    {{-- Form Edit --}}
    <form action="{{ route('petugas.keanggotaan.update', $anggota->id) }}" 
          method="POST" 
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        {{-- Informasi Dasar --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Informasi Dasar
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $anggota->name) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200" required>
                </div>
                
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $anggota->email) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200" required>
                </div>
                
                {{-- NISN/NIK --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NISN/NIK <span class="text-red-500">*</span></label>
                    <input type="text" name="nisn_nik" value="{{ old('nisn_nik', $anggota->nisn_nik) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200" required>
                </div>
                
                {{-- No Telepon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $anggota->phone) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>
        </div>

        {{-- Data Akademik (untuk siswa) --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                </svg>
                Data Akademik
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Jenis --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="jenis" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
                        <option value="siswa" {{ $anggota->jenis == 'siswa' ? 'selected' : '' }}>🎓 Siswa</option>
                        <option value="guru" {{ $anggota->jenis == 'guru' ? 'selected' : '' }}>👨‍🏫 Guru</option>
                        <option value="pegawai" {{ $anggota->jenis == 'pegawai' ? 'selected' : '' }}>💼 Pegawai</option>
                        <option value="umum" {{ $anggota->jenis == 'umum' ? 'selected' : '' }}>👤 Umum</option>
                    </select>
                </div>
                
                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $anggota->kelas) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200"
                           placeholder="Contoh: X, XI, XII">
                </div>
                
                {{-- Jurusan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ old('jurusan', $anggota->jurusan) }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200"
                           placeholder="Contoh: IPA, IPS">
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Alamat
            </h2>
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">{{ old('address', $anggota->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Status Keanggotaan --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Status Keanggotaan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Status Anggota --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status_anggota" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
                        <option value="pending" {{ $anggota->status_anggota == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="active" {{ $anggota->status_anggota == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                        <option value="inactive" {{ $anggota->status_anggota == 'inactive' ? 'selected' : '' }}>❌ Nonaktif</option>
                        <option value="rejected" {{ $anggota->status_anggota == 'rejected' ? 'selected' : '' }}>⛔ Ditolak</option>
                    </select>
                </div>
                
                {{-- Masa Berlaku --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku</label>
                    <input type="date" name="masa_berlaku" 
                           value="{{ old('masa_berlaku', $anggota->masa_berlaku ? \Carbon\Carbon::parse($anggota->masa_berlaku)->format('Y-m-d') : '') }}" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('petugas.keanggotaan.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Update Data
            </button>
        </div>
    </form>
</div>
@endsection