@extends('petugas.layouts.app')

@section('title', 'Manajemen Keanggotaan')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Manajemen Keanggotaan
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola pendaftaran dan data anggota perpustakaan</p>
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('petugas.keanggotaan.export') }}" 
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="hidden md:inline">Export Excel</span>
                </a>
                
                <a href="#" 
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    <span class="hidden md:inline">Tambah Anggota</span>
                </a>
            </div>
        </div>

        {{-- Statistik Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-indigo-600 text-2xl font-bold">{{ $statistik['total'] }}</div>
                        <div class="text-xs text-gray-500">Total Anggota</div>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-yellow-600 text-2xl font-bold">{{ $statistik['pending'] }}</div>
                        <div class="text-xs text-gray-500">Menunggu Verifikasi</div>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-green-600 text-2xl font-bold">{{ $statistik['active'] }}</div>
                        <div class="text-xs text-gray-500">Aktif</div>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 p-4 rounded-xl shadow-sm border border-red-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-red-600 text-2xl font-bold">{{ $statistik['inactive'] }}</div>
                        <div class="text-xs text-gray-500">Nonaktif</div>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-gray-600 text-2xl font-bold">{{ $statistik['rejected'] }}</div>
                        <div class="text-xs text-gray-500">Ditolak</div>
                    </div>
                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('petugas.keanggotaan.index') }}" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-3">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari nama, email, NISN/NIK, no anggota..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all">
                    </div>
                </div>
                
                {{-- Filter Status --}}
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Nonaktif</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>⛔ Ditolak</option>
                </select>
                
                {{-- Filter Jenis --}}
                <select name="jenis" class="px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua Jenis</option>
                    <option value="siswa" {{ request('jenis') == 'siswa' ? 'selected' : '' }}>🎓 Siswa</option>
                    <option value="guru" {{ request('jenis') == 'guru' ? 'selected' : '' }}>👨‍🏫 Guru</option>
                    <option value="pegawai" {{ request('jenis') == 'pegawai' ? 'selected' : '' }}>💼 Pegawai</option>
                    <option value="umum" {{ request('jenis') == 'umum' ? 'selected' : '' }}>👤 Umum</option>
                </select>
                
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                </button>
                
                @if(request()->anyFilled(['search', 'status', 'jenis']))
                    <a href="{{ route('petugas.keanggotaan.index') }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===== TABEL PENDING (YANG PERLU VERIFIKASI) ===== --}}
    @if($pending->count() > 0)
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-6 bg-yellow-500 rounded-full"></span>
                <span>Pendaftar Baru (Perlu Verifikasi)</span>
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">{{ $pending->count() }}</span>
            </h2>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
                            <th class="px-4 py-3 text-left w-16">No</th>
                            <th class="px-4 py-3 text-left">Tanggal Daftar</th>
                            <th class="px-4 py-3 text-left">Nama & Kontak</th>
                            <th class="px-4 py-3 text-left">NIK</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pending as $index => $item)
                        <tr class="hover:bg-yellow-50 transition-colors">
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                            
                            {{-- Tanggal Daftar --}}
                            <td class="px-4 py-3">
                                <div class="text-sm">{{ $item->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</div>
                            </td>
                            
                            {{-- Nama & Kontak --}}
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 mt-1 space-y-1">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $item->email }}
                                    </div>
                                    @if($item->phone)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $item->phone }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            
                            {{-- NIK --}}
                            <td class="px-4 py-3 font-mono text-sm">{{ $item->nisn_nik }}</td>
                            
                            {{-- Jenis --}}
                            <td class="px-4 py-3">
                                @php
                                    $jenisIcons = [
                                        'siswa' => '🎓',
                                        'guru' => '👨‍🏫',
                                        'pegawai' => '💼',
                                        'umum' => '👤'
                                    ];
                                    $icon = $jenisIcons[$item->jenis] ?? '👤';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $icon }} {{ ucfirst($item->jenis ?? 'umum') }}
                                </span>
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    ⏳ Menunggu
                                </span>
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('petugas.keanggotaan.show', $item->id) }}" 
                                       class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                       title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <button onclick="openApproveModal({{ $item->id }}, '{{ $item->name }}')"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Setujui">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    
                                    <button onclick="openRejectModal({{ $item->id }}, '{{ $item->name }}')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Tolak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== TABEL ANGGOTA AKTIF & LAINNYA ===== --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                <span>Daftar Anggota</span>
                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $anggota->total() }}</span>
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                            <th class="px-4 py-3 text-left w-16">No</th>
                            <th class="px-4 py-3 text-left">No. Anggota</th>
                            <th class="px-4 py-3 text-left">Foto</th>
                            <th class="px-4 py-3 text-left">Nama & Kontak</th>
                            <th class="px-4 py-3 text-left">Identitas</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Masa Berlaku</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($anggota as $index => $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-500">{{ $anggota->firstItem() + $index }}</td>
                            
                            {{-- No Anggota --}}
                            <td class="px-4 py-3 font-mono text-sm">
                                @if($item->no_anggota)
                                    <span class="font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                        {{ $item->no_anggota }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            
                            {{-- Foto --}}
                            <td class="px-4 py-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center overflow-hidden">
                                    @if($item->foto_ktp)
                                        <img src="{{ asset('storage/'.$item->foto_ktp) }}" 
                                             alt="{{ $item->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <span class="text-lg font-bold text-indigo-600">
                                            {{ strtoupper(substr($item->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            {{-- Nama & Kontak --}}
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 mt-1 space-y-1">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $item->email }}
                                    </div>
                                    @if($item->phone)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        {{ $item->phone }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            
                            {{-- Identitas --}}
                            <td class="px-4 py-3">
                                <div class="font-mono text-sm">{{ $item->nisn_nik }}</div>
                                @if($item->kelas)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Kelas: {{ $item->kelas }} {{ $item->jurusan }}
                                    </div>
                                @endif
                            </td>
                            
                            {{-- Jenis --}}
                            <td class="px-4 py-3">
                                @php
                                    $jenisStyles = [
                                        'siswa' => 'bg-blue-100 text-blue-800',
                                        'guru' => 'bg-green-100 text-green-800',
                                        'pegawai' => 'bg-purple-100 text-purple-800',
                                        'umum' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $jenisIcons = [
                                        'siswa' => '🎓',
                                        'guru' => '👨‍🏫',
                                        'pegawai' => '💼',
                                        'umum' => '👤'
                                    ];
                                    $style = $jenisStyles[$item->jenis] ?? 'bg-gray-100 text-gray-800';
                                    $icon = $jenisIcons[$item->jenis] ?? '👤';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $style }}">
                                    {{ $icon }} {{ ucfirst($item->jenis ?? 'umum') }}
                                </span>
                            </td>
                            
                            {{-- Status --}}
                            <td class="px-4 py-3">
                                @php
                                    $statusStyles = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-red-100 text-red-800',
                                        'rejected' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusIcons = [
                                        'pending' => '⏳',
                                        'active' => '✅',
                                        'inactive' => '❌',
                                        'rejected' => '⛔'
                                    ];
                                    $style = $statusStyles[$item->status_anggota] ?? 'bg-gray-100';
                                    $icon = $statusIcons[$item->status_anggota] ?? '❓';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $style }}">
                                    {{ $icon }} {{ ucfirst($item->status_anggota ?? 'unknown') }}
                                </span>
                            </td>
                            
                            {{-- Masa Berlaku --}}
                            <td class="px-4 py-3">
                                @if($item->masa_berlaku)
                                    <div class="text-sm">
                                        {{ \Carbon\Carbon::parse($item->masa_berlaku)->format('d/m/Y') }}
                                    </div>
                                    @if(\Carbon\Carbon::parse($item->masa_berlaku)->isPast() && $item->status_anggota == 'active')
                                        <span class="text-xs text-red-500">(Expired)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('petugas.keanggotaan.show', $item->id) }}" 
                                       class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                       title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('petugas.keanggotaan.edit', $item->id) }}" 
                                       class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    
                                    @if($item->status_anggota == 'active')
                                        <button onclick="openDeactivateModal({{ $item->id }}, '{{ $item->name }}')"
                                                class="p-2 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors"
                                                title="Nonaktifkan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    @if(in_array($item->status_anggota, ['inactive', 'rejected']))
                                        <form action="{{ route('petugas.keanggotaan.activate', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                                    title="Aktifkan Kembali"
                                                    onclick="return confirm('Aktifkan kembali anggota {{ $item->name }}?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <svg class="w-20 h-20 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-lg">Tidak ada data anggota</p>
                                    <p class="text-sm text-gray-400 mt-1">Belum ada anggota terdaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($anggota->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                    {{ $anggota->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
@include('petugas.pages.keanggotaan.partials.modals')
@endsection