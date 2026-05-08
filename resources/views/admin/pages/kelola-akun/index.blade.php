@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-biru-50 p-4 md:p-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-hitam-800 mb-2">Kelola Akun Pengguna</h1>
                <p class="text-hitam-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <span>Total {{ $users->total() }} akun • {{ $users->where('status', 'active')->count() }} aktif</span>
                </p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <!-- Import Excel Button -->
                <button type="button"
                        x-data
                        @click="$dispatch('open-import-modal')"
                        class="inline-flex items-center gap-2 px-4 py-3 bg-hijau-500 hover:bg-hijau-600 text-white rounded-lg font-medium transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Import Excel
                </button>
                
                <!-- Add Account Button -->
                <a href="{{ route('admin.kelola-akun.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-biru-600 to-biru-700 hover:from-biru-700 hover:to-biru-800 text-white rounded-lg font-medium transition-all shadow-sm hover:shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Akun Baru
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Total Akun</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-biru-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-biru-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Siswa</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('role', 'siswa')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-hijau-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">🎓</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Guru</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('role', 'guru')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">👨‍🏫</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-hitam-500">Aktif</p>
                        <p class="text-2xl font-bold text-hitam-800">{{ $users->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-hijau-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-hijau-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <form method="GET" action="{{ route('admin.kelola-akun.index') }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, NISN, atau NIK..."
                            class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="flex gap-3">
                    <select name="role" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Semua Role</option>
                        <option value="siswa" {{ request('role')=='siswa'?'selected':'' }}>Siswa</option>
                        <option value="guru" {{ request('role')=='guru'?'selected':'' }}>Guru</option>
                        <option value="pegawai" {{ request('role')=='pegawai'?'selected':'' }}>Pegawai</option>
                        <option value="umum" {{ request('role')=='umum'?'selected':'' }}>Umum</option>
                    </select>

                    <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
                    </select>

                    <button type="submit"
                            class="px-4 py-3 bg-biru-600 text-white rounded-lg hover:bg-biru-700">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ===== TABLE SECTION ===== -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-biru-600 to-biru-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            No. Anggota
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Pengguna
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Identitas
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Peran
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-hitam-500">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->no_anggota)
                                <span class="font-mono text-sm font-medium text-biru-600 bg-biru-50 px-2 py-1 rounded">
                                    {{ $user->no_anggota }}
                                </span>
                            @else
                                <span class="text-hitam-400 text-xs">-</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br 
                                        @if($user->role === 'siswa') from-biru-100 to-biru-200 text-biru-600
                                        @elseif($user->role === 'guru') from-hijau-100 to-hijau-200 text-hijau-600
                                        @elseif($user->role === 'pegawai') from-purple-100 to-purple-200 text-purple-600
                                        @else from-gray-100 to-gray-200 text-hitam-600 @endif
                                        flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-hitam-800">{{ $user->name }}</div>
                                    @if($user->email)
                                    <div class="text-sm text-hitam-500">{{ $user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm text-hitam-800 font-mono">{{ $user->nisn_nik }}</div>
                            @if($user->kelas || $user->jurusan)
                                <div class="text-xs text-hitam-500 mt-1">
                                    {{ $user->kelas }} {{ $user->jurusan }}
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($user->role === 'siswa') bg-biru-100 text-biru-800
                                @elseif($user->role === 'guru') bg-hijau-100 text-hijau-800
                                @elseif($user->role === 'pegawai') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-hitam-800 @endif">
                                @if($user->role === 'siswa') 🎓
                                @elseif($user->role === 'guru') 👨‍🏫
                                @elseif($user->role === 'pegawai') 💼
                                @else 👤 @endif
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($user->status === 'active')
                                    bg-hijau-100 text-hijau-800
                                @else
                                    bg-oren-100 text-oren-800
                                @endif">
                                <span class="w-2 h-2 rounded-full mr-1.5
                                    @if($user->status === 'active')
                                        bg-hijau-500
                                    @else
                                        bg-oren-500
                                    @endif">
                                </span>
                                {{ $user->status === 'active' ? 'AKTIF' : 'NONAKTIF' }}
                            </span>
                            
                            @if($user->masa_berlaku)
                                <div class="text-xs text-hitam-500 mt-1">
                                    Berlaku: {{ \Carbon\Carbon::parse($user->masa_berlaku)->format('d/m/Y') }}
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <!-- Edit Button -->
                                <a href="{{ route('admin.kelola-akun.edit', $user->id) }}" 
                                class="p-2 text-biru-600 hover:bg-biru-50 rounded-lg transition-colors duration-200 group relative"
                                title="Edit Akun">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-hitam-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        Edit Akun
                                    </span>
                                </a>
                                
                                <!-- Reset Password Button with Modal -->
                                <div x-data="{ 
                                    showResetConfirm: false,
                                    resetPassword() {
                                        this.showResetConfirm = false;
                                        this.$refs.resetForm.submit();
                                    }
                                }" class="relative">
                                    <button type="button"
                                            @click="showResetConfirm = true"
                                            class="p-2 text-oren-600 hover:bg-oren-50 rounded-lg transition-colors duration-200 group relative"
                                            title="Reset Password">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-hitam-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                            Reset Password
                                        </span>
                                    </button>
                                    
                                    <!-- Reset Password Confirmation Modal -->
                                    <div x-show="showResetConfirm" 
                                        x-cloak
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        @keydown.escape.window="showResetConfirm = false">
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" @click="showResetConfirm = false">
                                                <div class="absolute inset-0 bg-hitam-800 opacity-75"></div>
                                            </div>
                                            
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            
                                            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <!-- Modal Header -->
                                                <div class="bg-gradient-to-r from-oren-500 to-oren-600 px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="bg-white rounded-full p-2 mr-3">
                                                            <svg class="w-6 h-6 text-oren-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-lg font-semibold text-white">
                                                                Konfirmasi Reset Password
                                                            </h3>
                                                            <p class="text-oren-100 text-sm mt-1">
                                                                Password akan direset ke NISN/NIK pengguna
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Body -->
                                                <div class="px-6 py-6">
                                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                                        <div class="flex">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-sm text-yellow-700">
                                                                    Password akan direset ke <strong>{{ $user->nisn_nik }}</strong>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-3">
                                                        <div class="flex items-start space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <svg class="w-5 h-5 text-hitam-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-medium text-hitam-800">{{ $user->name }}</p>
                                                                <p class="text-xs text-hitam-500">{{ $user->email ?? 'No email' }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-start space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <svg class="w-5 h-5 text-hitam-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm text-hitam-600">Role: <span class="font-medium">{{ strtoupper($user->role) }}</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                                        <p class="text-sm text-hitam-600">
                                                            <span class="font-semibold">Informasi Penting:</span><br>
                                                            • Password akan direset ke NISN/NIK pengguna<br>
                                                            • Pengguna akan diminta mengganti password saat login berikutnya<br>
                                                            • Pastikan NISN/NIK yang tercatat sudah benar
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Footer -->
                                                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                                                    <button type="button"
                                                            @click="showResetConfirm = false"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-hitam-700 hover:bg-gray-100 transition-colors">
                                                        Batal
                                                    </button>
                                                    <button type="button"
                                                            @click="resetPassword()"
                                                            class="px-4 py-2 bg-gradient-to-r from-oren-500 to-oren-600 text-white rounded-lg hover:from-oren-600 hover:to-oren-700 transition-all shadow-sm">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                            </svg>
                                                            Ya, Reset Password
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden Form for Reset -->
                                    <form x-ref="resetForm" 
                                        action="{{ route('admin.kelola-akun.reset', $user->id) }}" 
                                        method="POST" 
                                        class="hidden">
                                        @csrf
                                    </form>
                                </div>
                                
                                <!-- Delete Button with Modal -->
                                <div x-data="{ 
                                    showDeleteConfirm: false,
                                    confirmDelete: false,
                                    deleteAccount() {
                                        if (this.confirmDelete) {
                                            this.showDeleteConfirm = false;
                                            this.$refs.deleteForm.submit();
                                        }
                                    }
                                }" class="relative">
                                    <button type="button"
                                            @click="showDeleteConfirm = true; confirmDelete = false"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 group relative"
                                            title="Hapus Akun">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-hitam-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                            Hapus Akun
                                        </span>
                                    </button>
                                    
                                    <!-- Delete Confirmation Modal -->
                                    <div x-show="showDeleteConfirm" 
                                        x-cloak
                                        class="fixed inset-0 z-50 overflow-y-auto"
                                        @keydown.escape.window="showDeleteConfirm = false">
                                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 transition-opacity" @click="showDeleteConfirm = false">
                                                <div class="absolute inset-0 bg-hitam-800 opacity-75"></div>
                                            </div>
                                            
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                            
                                            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <!-- Modal Header -->
                                                <div class="bg-gradient-to-r from-red-500 to-rose-500 px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="bg-white rounded-full p-2 mr-3">
                                                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-lg font-semibold text-white">
                                                                Konfirmasi Hapus Akun
                                                            </h3>
                                                            <p class="text-red-100 text-sm mt-1">
                                                                Tindakan ini tidak dapat dibatalkan!
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Body -->
                                                <div class="px-6 py-6">
                                                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                                                        <div class="flex">
                                                            <div class="flex-shrink-0">
                                                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                            </div>
                                                            <div class="ml-3">
                                                                <p class="text-sm text-red-700 font-medium">
                                                                    Anda akan menghapus akun berikut secara permanen:
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-3 mb-4">
                                                        <div class="flex items-start space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <svg class="w-5 h-5 text-hitam-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-medium text-hitam-800">{{ $user->name }}</p>
                                                                <p class="text-xs text-hitam-500">{{ $user->email ?? 'No email' }}</p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-start space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <svg class="w-5 h-5 text-hitam-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm text-hitam-600">No. Anggota: <span class="font-mono font-medium">{{ $user->no_anggota ?? '-' }}</span></p>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-start space-x-3">
                                                            <div class="flex-shrink-0">
                                                                <svg class="w-5 h-5 text-hitam-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm text-hitam-600">Role: <span class="font-medium">{{ strtoupper($user->role) }}</span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                                                        <p class="text-sm font-semibold text-yellow-800 mb-2">⚠️ Data yang akan ikut terhapus:</p>
                                                        <ul class="text-xs text-yellow-700 space-y-1">
                                                            <li class="flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Data profil dan akun
                                                            </li>
                                                            <li class="flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Riwayat peminjaman buku
                                                            </li>
                                                            <li class="flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Data denda (jika ada)
                                                            </li>
                                                            <li class="flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Riwayat kunjungan
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    
                                                    <!-- Konfirmasi Checkbox -->
                                                    <div class="border-2 border-red-200 rounded-lg p-4 bg-white">
                                                        <label class="flex items-start gap-3 cursor-pointer">
                                                            <input type="checkbox" 
                                                                x-model="confirmDelete"
                                                                class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">
                                                            <span class="text-sm text-hitam-700">
                                                                Saya memahami bahwa tindakan ini <strong class="text-red-600">tidak dapat dibatalkan</strong> dan semua data terkait akan dihapus secara permanen dari sistem.
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <!-- Modal Footer -->
                                                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                                                    <button type="button"
                                                            @click="showDeleteConfirm = false; confirmDelete = false"
                                                            class="px-4 py-2 border border-gray-300 rounded-lg text-hitam-700 hover:bg-gray-100 transition-colors">
                                                        Batal
                                                    </button>
                                                    <button type="button"
                                                            @click="deleteAccount()"
                                                            :disabled="!confirmDelete"
                                                            :class="confirmDelete ? 'bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600' : 'bg-gray-300 cursor-not-allowed'"
                                                            class="px-4 py-2 text-white rounded-lg transition-all shadow-sm disabled:opacity-50">
                                                        <span class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Ya, Hapus Permanen
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden Form for Delete -->
                                    <form x-ref="deleteForm" 
                                        action="{{ route('admin.kelola-akun.destroy', $user->id) }}" 
                                        method="POST" 
                                        class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-hitam-400">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13-5.75a4 4 0 01-4 4m4-4a4 4 0 00-4-4m-12 8a4 4 0 114 4m-4-4a4 4 0 104 4m12-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-hitam-800 mb-2">Belum ada akun</p>
                                <a href="{{ route('admin.kelola-akun.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-biru-600 text-white rounded-lg">
                                    Tambah Akun Pertama
                                </a>
                            </div>
                        </td>
                    </table>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    <!-- ===== END TABLE SECTION ===== -->
</div>

<!-- IMPORT EXCEL MODAL -->
<div x-data="{ importModal: false }" 
     @open-import-modal.window="importModal = true"
     x-cloak>
    
    <div x-show="importModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="importModal = false">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"
                 @click="importModal = false">
                <div class="absolute inset-0 bg-hitam-800 opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-hijau-600 to-hijau-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-white">
                            Import Data dari Excel
                        </h3>
                        <button @click="importModal = false" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Form -->
                <form method="POST" action="{{ route('admin.kelola-akun.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="px-6 py-6">
                        <!-- File Upload -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-hitam-700 mb-2">
                                File Excel <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   name="excel_file" 
                                   accept=".xlsx,.xls,.csv"
                                   class="w-full border border-gray-300 rounded-lg p-2"
                                   required>
                        </div>
                        
                        <!-- Info -->
                        <div class="bg-biru-50 border border-biru-100 rounded-lg p-4 mb-4">
                            <p class="text-sm text-biru-800">
                                Format: NISN_NIK, Name, Email, Role (siswa/guru/pegawai/umum)
                            </p>
                        </div>
                        
                        <!-- Template Link -->
                        <div class="mb-6">
                            <a href="{{ route('admin.kelola-akun.template') }}" 
                               class="text-hijau-600 hover:text-hijau-800">
                                Download Template Excel
                            </a>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button type="button"
                                @click="importModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-hitam-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-hijau-600 text-white rounded-lg hover:bg-hijau-700">
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>