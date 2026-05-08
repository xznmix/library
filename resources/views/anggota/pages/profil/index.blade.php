@extends('anggota.layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-gray-600 mt-1">Kelola informasi akun Anda</p>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    {{-- Error Validation --}}
    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
        <div class="font-medium mb-1">Terjadi kesalahan:</div>
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- ============================================================ --}}
        {{-- SIDEBAR KIRI - FOTO PROFIL --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                {{-- Foto Profil --}}
                <div class="relative inline-block">
                    <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 p-1">
                        <div class="w-full h-full rounded-full bg-white overflow-hidden flex items-center justify-center">
                            @if($user->foto_ktp && Storage::disk('public')->exists($user->foto_ktp))
                                <img src="{{ asset('storage/' . $user->foto_ktp) }}?v={{ time() }}" 
                                     alt="{{ $user->name }}" 
                                     id="profilePhoto"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-4xl font-bold text-indigo-600">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Tombol Upload --}}
                    <label for="upload_foto" 
                           class="absolute bottom-0 right-0 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-2 cursor-pointer shadow-lg transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </label>
                    
                    <form id="formUploadFoto" action="{{ route('anggota.profil.upload-foto') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="upload_foto" name="foto" accept="image/jpeg,image/png,image/jpg">
                    </form>
                </div>

                {{-- Nama & Role --}}
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 capitalize">{{ $user->jenis ?? 'Anggota' }}</p>
                
                {{-- No Anggota --}}
                @if($user->no_anggota)
                <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    {{ $user->no_anggota }}
                </div>
                @endif

                {{-- Status --}}
                <div class="mt-4">
                    @if($user->status_anggota == 'active')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                            Aktif
                        </span>
                    @elseif($user->status_anggota == 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                            Tidak Aktif
                        </span>
                    @endif
                </div>

                {{-- Informasi Singkat --}}
                <div class="mt-6 pt-4 border-t border-gray-200 text-left space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Email</span>
                        <span class="text-gray-900 font-medium truncate ml-2">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Telepon</span>
                        <span class="text-gray-900">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Bergabung</span>
                        <span class="text-gray-900">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
                    </div>
                    @if($user->masa_berlaku)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Masa Berlaku</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($user->masa_berlaku)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Statistik --}}
                <div class="mt-6 pt-4 border-t border-gray-200 grid grid-cols-2 gap-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $user->peminjaman ? $user->peminjaman->count() : 0 }}</p>
                        <p class="text-xs text-gray-500">Total Pinjaman</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $user->peminjaman ? $user->peminjaman->where('status_pinjam', 'dipinjam')->count() : 0 }}</p>
                        <p class="text-xs text-gray-500">Sedang Dipinjam</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- FORM UTAMA - INFORMASI PRIBADI --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Pribadi</h3>
                            <p class="text-sm text-gray-500">Perbarui data diri Anda</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('anggota.profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nama Lengkap --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                           required>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                           required>
                                </div>
                            </div>

                            {{-- Nomor Telepon --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="phone" 
                                           value="{{ old('phone', $user->phone) }}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                           placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            {{-- NISN/NIK --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    NISN / NIK
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           value="{{ $user->nisn_nik }}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                                           readonly
                                           disabled>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Tidak dapat diubah</p>
                            </div>

                            {{-- Kelas / Jurusan --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Kelas / Jurusan
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           value="{{ $user->kelas ?? '-' }} {{ $user->jurusan ?? '' }}"
                                           class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed"
                                           readonly
                                           disabled>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Alamat
                                </label>
                                <div class="relative">
                                    <div class="absolute top-3 left-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <textarea name="address" 
                                              rows="3"
                                              class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition shadow-sm flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- CARD UBAH PASSWORD --}}
            {{-- ============================================================ --}}
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Keamanan Akun</h3>
                            <p class="text-sm text-gray-500">Perbarui password untuk menjaga keamanan akun Anda</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="max-w-lg mx-auto">
                        {{-- Password Saat Ini --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password Saat Ini
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" 
                                       id="current_password"
                                       class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="Masukkan password saat ini">
                                <button type="button" 
                                        onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" 
                                       id="new_password"
                                       class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="Minimal 8 karakter">
                                <button type="button" 
                                        onclick="togglePassword('new_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Password harus terdiri dari minimal 8 karakter</span>
                            </div>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <input type="password" 
                                       id="new_password_confirmation"
                                       class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                       placeholder="Ketik ulang password baru">
                                <button type="button" 
                                        onclick="togglePassword('new_password_confirmation')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Strength Indicator --}}
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-500">Kekuatan Password</span>
                                <span id="passwordStrengthText" class="text-xs font-medium text-gray-500">Belum diisi</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="passwordStrengthBar" class="bg-gray-300 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>

                        {{-- Tombol Ganti Password --}}
                        <button onclick="changePassword()" 
                                class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Ganti Password
                        </button>

                        {{-- Informasi Keamanan --}}
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Tips Keamanan:</p>
                                    <ul class="list-disc list-inside space-y-1 text-xs">
                                        <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                                        <li>Jangan gunakan password yang sama dengan akun lain</li>
                                        <li>Hindari informasi pribadi seperti nama atau tanggal lahir</li>
                                        <li>Ganti password secara berkala setiap 3-6 bulan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SCRIPTS --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============================================================
// UPLOAD FOTO
// ============================================================
document.getElementById('upload_foto').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        
        // Validasi tipe file
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Hanya file gambar yang diperbolehkan (JPG, PNG, JPEG)',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }
        
        // Validasi ukuran file (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Ukuran file maksimal 2MB',
                confirmButtonColor: '#4f46e5'
            });
            return;
        }
        
        const formData = new FormData();
        formData.append('foto', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        Swal.fire({
            title: 'Mengupload...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("anggota.profil.upload-foto") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const fotoElement = document.getElementById('profilePhoto');
                if (fotoElement) {
                    fotoElement.src = data.foto_url + '?t=' + new Date().getTime();
                } else {
                    location.reload();
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    confirmButtonColor: '#4f46e5',
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    confirmButtonColor: '#4f46e5'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan. Silakan coba lagi.',
                confirmButtonColor: '#4f46e5'
            });
        });
        
        e.target.value = '';
    }
});

// ============================================================
// TOGGLE PASSWORD VISIBILITY
// ============================================================
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
}

// ============================================================
// PASSWORD STRENGTH CHECKER
// ============================================================
document.getElementById('new_password')?.addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    
    let strength = 0;
    let color = 'bg-gray-300';
    let text = 'Sangat Lemah';
    
    if (password.length > 0) {
        // Length check
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 10;
        
        // Uppercase check
        if (/[A-Z]/.test(password)) strength += 20;
        
        // Lowercase check
        if (/[a-z]/.test(password)) strength += 15;
        
        // Number check
        if (/[0-9]/.test(password)) strength += 15;
        
        // Special character check
        if (/[^A-Za-z0-9]/.test(password)) strength += 15;
        
        strength = Math.min(strength, 100);
        
        if (strength >= 80) {
            color = 'bg-green-500';
            text = 'Kuat';
        } else if (strength >= 60) {
            color = 'bg-blue-500';
            text = 'Sedang';
        } else if (strength >= 40) {
            color = 'bg-yellow-500';
            text = 'Lemah';
        } else {
            color = 'bg-red-500';
            text = 'Sangat Lemah';
        }
    } else {
        strength = 0;
        text = 'Belum diisi';
    }
    
    strengthBar.style.width = strength + '%';
    strengthBar.className = `h-1.5 rounded-full transition-all duration-300 ${color}`;
    strengthText.textContent = text;
    strengthText.className = `text-xs font-medium ${color.replace('bg-', 'text-')}`;
});

// ============================================================
// PASSWORD CONFIRMATION REALTIME
// ============================================================
document.getElementById('new_password_confirmation')?.addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword.length > 0) {
        if (newPassword !== confirmPassword) {
            this.classList.add('border-red-500');
            this.classList.remove('border-green-500');
        } else {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
        }
    } else {
        this.classList.remove('border-red-500', 'border-green-500');
    }
});

// ============================================================
// CHANGE PASSWORD
// ============================================================
function changePassword() {
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const newPasswordConfirmation = document.getElementById('new_password_confirmation').value;
    
    if (!currentPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Password saat ini harus diisi!',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }
    
    if (!newPassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Password baru harus diisi!',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }
    
    if (newPassword.length < 8) {
        Swal.fire({
            icon: 'warning',
            title: 'Password Terlalu Pendek',
            text: 'Password baru minimal 8 karakter!',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }
    
    if (newPassword !== newPasswordConfirmation) {
        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Gagal',
            text: 'Konfirmasi password tidak cocok!',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }
    
    Swal.fire({
        title: 'Yakin ingin mengubah password?',
        text: 'Anda akan logout dan harus login dengan password baru',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('{{ route("anggota.profil.change-password") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: newPasswordConfirmation
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#3B82F6'
                    }).then(() => {
                        // Redirect ke halaman login
                        window.location.href = data.redirect || '{{ route("login") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message,
                        confirmButtonColor: '#3B82F6'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#3B82F6'
                });
            });
        }
    });
}
</script>
@endsection