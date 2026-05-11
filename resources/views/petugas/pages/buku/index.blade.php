@extends('petugas.layouts.app')

@section('title', 'Manajemen Buku')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header dengan Statistik Cepat --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📚 Manajemen Buku</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola koleksi buku perpustakaan</p>
            </div>
            
            {{-- Tombol Aksi --}}
            <div class="flex gap-2">
                {{-- Tombol Import Excel dengan Alpine.js --}}
                <button type="button" 
                        x-data
                        @click="$dispatch('open-import-modal')"
                        class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg shadow-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="font-medium">Import Excel</span>
                </button>
                
                {{-- Tombol Tambah Buku --}}
                <a href="{{ route('petugas.buku.create') }}" 
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium">Tambah Buku Baru</span>
                </a>
            </div>
        </div>

        {{-- Notifikasi Import Success dengan Cover Reminder --}}
        @if(session('success') && (str_contains(session('success'), 'import') || str_contains(session('success'), 'Import')))
        <div class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    
                    @if(session('import_success_count') && session('import_success_count') > 0)
                    <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">📷 Silahkan tambahkan gambar cover untuk buku yang baru diimport!</p>
                                <p class="text-xs mt-1">Klik tombol edit pada buku yang ingin ditambahkan sampul.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Notifikasi Umum Success --}}
        @if(session('success') && !str_contains(session('success'), 'import'))
        <div class="mt-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Statistik Cepat --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-indigo-600 text-2xl font-bold">{{ $totalBuku ?? 0 }}</div>
                <div class="text-xs text-gray-500">Total Buku</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-green-600 text-2xl font-bold">{{ $totalTersedia ?? 0 }}</div>
                <div class="text-xs text-gray-500">Tersedia</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-yellow-600 text-2xl font-bold">{{ $totalDipinjam ?? 0 }}</div>
                <div class="text-xs text-gray-500">Dipinjam</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-blue-600 text-2xl font-bold">{{ $totalKategori ?? 0 }}</div>
                <div class="text-xs text-gray-500">Kategori Buku</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-purple-600 text-2xl font-bold">{{ $totalEbook ?? 0 }}</div>
                <div class="text-xs text-gray-500">E-Book</div>
            </div>
        </div>
    </div>

    {{-- Filter dan Pencarian --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form method="GET" action="{{ route('petugas.buku.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul, pengarang, ISBN, atau barcode..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition-all">
                </div>
            </div>
            <div class="flex gap-2">
                <select name="kategori" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList ?? [] as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
                <select name="tipe" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                    <option value="">Semua Tipe</option>
                    <option value="fisik" {{ request('tipe') == 'fisik' ? 'selected' : '' }}>📖 Fisik</option>
                    <option value="digital" {{ request('tipe') == 'digital' ? 'selected' : '' }}>💻 Digital</option>
                </select>
                <select name="kategori_koleksi" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-200">
                    <option value="">Semua Koleksi</option>
                    <option value="buku_paket" {{ request('kategori_koleksi') == 'buku_paket' ? 'selected' : '' }}>📚 Buku Paket</option>
                    <option value="fisik" {{ request('kategori_koleksi') == 'fisik' ? 'selected' : '' }}>📖 Koleksi Fisik</option>
                    <option value="referensi" {{ request('kategori_koleksi') == 'referensi' ? 'selected' : '' }}>📕 Referensi</option>
                    <option value="non_fiksi" {{ request('kategori_koleksi') == 'non_fiksi' ? 'selected' : '' }}>📗 Non Fiksi</option>
                    <option value="umum" {{ request('kategori_koleksi') == 'umum' ? 'selected' : '' }}>📘 Umum</option>
                    <option value="paket" {{ request('kategori_koleksi') == 'paket' ? 'selected' : '' }}>📙 Paket</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'kategori', 'tipe', 'kategori_koleksi']))
                    <a href="{{ route('petugas.buku.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Data Buku --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left w-12">No</th>
                        <th class="px-4 py-3 text-left">Sampul</th>
                        <th class="px-4 py-3 text-left">Judul & Pengarang</th>
                        <th class="px-4 py-3 text-left">Barcode</th>
                        <th class="px-4 py-3 text-left">Kategori Koleksi</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-center">Stok</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($buku as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500">{{ $buku->firstItem() + $index }}</td>
                        
                        {{-- Sampul Buku --}}
                        <td class="px-4 py-3">
                            <div class="w-12 h-16 bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                @if($item->sampul && Storage::disk('public')->exists($item->sampul))
                                    <img src="{{ asset('storage/'.$item->sampul) }}" 
                                         alt="{{ $item->judul }}"
                                         class="w-full h-full object-cover">
                                @elseif($item->cover_path && Storage::disk('public')->exists($item->cover_path))
                                    <img src="{{ asset('storage/'.$item->cover_path) }}" 
                                         alt="{{ $item->judul }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Informasi Buku --}}
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-800">{{ $item->judul }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                @if($item->pengarang)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        {{ $item->pengarang }}
                                    </span>
                                @endif
                                @if($item->penerbit)
                                    <span class="inline-flex items-center gap-1 ml-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                                        </svg>
                                        {{ $item->penerbit }}
                                    </span>
                                @endif
                                @if($item->isbn)
                                    <span class="inline-flex items-center gap-1 ml-3">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                                        </svg>
                                        ISBN: {{ $item->isbn }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Barcode --}}
                        <td class="px-4 py-3">
                            @if($item->barcode)
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $item->barcode }}</span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>

                        {{-- Kategori Koleksi --}}
                        <td class="px-4 py-3">
                            @php
                                $koleksiLabels = [
                                    'buku_paket' => ['label' => '📚 Buku Paket', 'color' => 'bg-blue-100 text-blue-800'],
                                    'fisik' => ['label' => '📖 Koleksi Fisik', 'color' => 'bg-green-100 text-green-800'],
                                    'referensi' => ['label' => '📕 Referensi', 'color' => 'bg-purple-100 text-purple-800'],
                                    'non_fiksi' => ['label' => '📗 Non Fiksi', 'color' => 'bg-orange-100 text-orange-800'],
                                    'umum' => ['label' => '📘 Umum', 'color' => 'bg-indigo-100 text-indigo-800'],
                                    'paket' => ['label' => '📙 Paket', 'color' => 'bg-pink-100 text-pink-800'],
                                ];
                                $koleksi = $koleksiLabels[$item->kategori_koleksi] ?? ['label' => '📚 Umum', 'color' => 'bg-gray-100 text-gray-800'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $koleksi['color'] }}">
                                {{ $koleksi['label'] }}
                            </span>
                        </td>

                        {{-- Kategori Buku --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                {{ $item->kategori->nama ?? 'Tanpa Kategori' }}
                            </span>
                        </td>

                        {{-- Tipe --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium 
                                {{ $item->tipe == 'fisik' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($item->tipe == 'fisik')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    @endif
                                </svg>
                                {{ ucfirst($item->tipe) }}
                            </span>
                            @if($item->tipe == 'digital' && $item->durasi_pinjam_hari)
                                <div class="text-xs text-gray-400 mt-1">Pinjam: {{ $item->durasi_pinjam_hari }} hari</div>
                            @endif
                        </td>

                        {{-- Stok --}}
                        <td class="px-4 py-3 text-center">
                            <div class="font-medium">{{ $item->stok_tersedia }}/{{ $item->stok }}</div>
                            @if($item->stok_rusak > 0)
                                <div class="text-xs text-red-500">Rusak: {{ $item->stok_rusak }}</div>
                            @endif
                            @if($item->stok_hilang > 0)
                                <div class="text-xs text-gray-500">Hilang: {{ $item->stok_hilang }}</div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusColors = [
                                    'tersedia' => 'bg-green-50 text-green-700',
                                    'dipinjam' => 'bg-yellow-50 text-yellow-700',
                                    'rusak' => 'bg-red-50 text-red-700',
                                    'hilang' => 'bg-gray-50 text-gray-700',
                                    'dipesan' => 'bg-blue-50 text-blue-700'
                                ];
                                $color = $statusColors[$item->status] ?? 'bg-gray-50 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $color }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                    {{ $item->status == 'tersedia' ? 'bg-green-500' : '' }}
                                    {{ $item->status == 'dipinjam' ? 'bg-yellow-500' : '' }}
                                    {{ $item->status == 'rusak' ? 'bg-red-500' : '' }}
                                    {{ $item->status == 'hilang' ? 'bg-gray-500' : '' }}
                                    {{ $item->status == 'dipesan' ? 'bg-blue-500' : '' }}">
                                </span>
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="showDetail({{ $item->id }})" 
                                        class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Detail Buku">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>

                                <a href="{{ route('petugas.buku.edit', $item->id) }}" 
                                   class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Edit Buku">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <button type="button" 
                                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')" 
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Hapus Buku">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>

                                <form id="delete-form-{{ $item->id }}" 
                                    action="{{ route('petugas.buku.destroy', $item->id) }}" 
                                    method="POST" 
                                    style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Belum ada data buku</p>
                                <p class="text-sm text-gray-400 mt-1">Klik tombol "Tambah Buku Baru" untuk memulai, atau "Import Excel" untuk import massal</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($buku->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $buku->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL IMPORT EXCEL dengan Alpine.js --}}
<div x-data="{ importModal: false, fileName: '' }" 
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
            <div class="fixed inset-0 transition-opacity" @click="importModal = false">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75"></div>
            </div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 py-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Import Data Buku</h3>
                                <p class="text-sm text-gray-500">Upload file Excel untuk menambah buku secara massal</p>
                            </div>
                        </div>
                        <button @click="importModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('petugas.buku.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih File Excel <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition-colors cursor-pointer"
                                 @click="$refs.fileInput.click()">
                                <input type="file" 
                                       x-ref="fileInput"
                                       name="file_excel" 
                                       accept=".xlsx,.xls,.csv"
                                       class="hidden"
                                       @change="fileName = $refs.fileInput.files[0]?.name || ''"
                                       required>
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600" x-text="fileName || 'Klik untuk upload atau drag file'"></p>
                                <p class="text-xs text-gray-500">Format: XLSX, XLS, CSV (max 5MB)</p>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium">📋 Petunjuk Import:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1 text-xs">
                                        <li>Gunakan template yang sudah disediakan</li>
                                        <li>Kolom <strong>judul</strong> wajib diisi</li>
                                        <li>Barcode akan digenerate otomatis oleh sistem</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('petugas.buku.download-template') }}" 
                               class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download Template
                            </a>
                            <button type="submit" 
                                    id="importSubmitBtn"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Buku --}}
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div id="modalContent" class="p-6">
                <div class="flex justify-center items-center h-32">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, judul) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        html: `Anda akan menghapus buku <strong class="text-red-600">"${judul}"</strong><br>Semua data terkait akan ikut terhapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        }
    });
}

function showDetail(id) {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.classList.remove('hidden');
    
    fetch(`/petugas/buku/${id}`)
        .then(response => response.json())
        .then(data => {
            let coverHtml = '';
            if (data.sampul) {
                coverHtml = `<img src="/storage/${data.sampul}" alt="${data.judul}" class="w-full h-full object-cover">`;
            } else if (data.cover_path) {
                coverHtml = `<img src="/storage/${data.cover_path}" alt="${data.judul}" class="w-full h-full object-cover">`;
            } else {
                coverHtml = `<div class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>`;
            }
            
            function formatDate(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '-';
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            }
            
            let kategoriKoleksiLabel = '📘 Umum';
            switch(data.kategori_koleksi) {
                case 'buku_paket': kategoriKoleksiLabel = '📚 Buku Paket'; break;
                case 'fisik': kategoriKoleksiLabel = '📖 Koleksi Fisik'; break;
                case 'referensi': kategoriKoleksiLabel = '📕 Referensi'; break;
                case 'non_fiksi': kategoriKoleksiLabel = '📗 Non Fiksi'; break;
                case 'paket': kategoriKoleksiLabel = '📙 Paket'; break;
                default: kategoriKoleksiLabel = '📘 Umum'; break;
            }
            
            let statusHtml = '';
            if (data.tipe === 'fisik') {
                if (data.stok_tersedia > 0) {
                    statusHtml = `<span class="text-green-600 font-medium">✓ Tersedia (${data.stok_tersedia}/${data.stok})</span>`;
                } else {
                    statusHtml = `<span class="text-red-600 font-medium">✗ Dipinjam</span>`;
                }
            } else {
                statusHtml = `<span class="text-green-600 font-medium">✓ Tersedia (E-book)</span>`;
                if (data.durasi_pinjam_hari) {
                    statusHtml += `<div class="text-xs text-gray-500 mt-1">Durasi pinjam: ${data.durasi_pinjam_hari} hari</div>`;
                }
            }
            
            let sumberText = '-';
            if (data.sumber_jenis) {
                const sumberJenisMap = {
                    'pembelian': 'Pembelian',
                    'hadiah_hibah': 'Hadiah/Hibah',
                    'penggantian': 'Penggantian',
                    'penggandaan': 'Penggandaan',
                    'tukar_menukar': 'Tukar Menukar',
                    'terbitan_sendiri': 'Terbitan Sendiri',
                    'deposit': 'Deposit'
                };
                sumberText = sumberJenisMap[data.sumber_jenis] || data.sumber_jenis;
                if (data.sumber_nama) {
                    sumberText += ` (${data.sumber_nama})`;
                }
            } else if (data.sumber_nama) {
                sumberText = data.sumber_nama;
            }
            
            modalContent.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Buku</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <div class="w-full aspect-[2/3] bg-gray-100 rounded-lg overflow-hidden">
                            ${coverHtml}
                        </div>
                        <div class="mt-2 text-center">
                            <span class="inline-block px-2 py-1 text-xs rounded-full
                                ${data.tipe === 'fisik' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'}">
                                ${data.tipe === 'fisik' ? '📖 Fisik' : '💻 Digital'}
                            </span>
                        </div>
                    </div>
                    <div class="col-span-2 space-y-3">
                        <div>
                            <h4 class="text-xl font-bold text-gray-900">${escapeHtml(data.judul)}</h4>
                            ${data.sub_judul ? `<p class="text-sm text-gray-500">${escapeHtml(data.sub_judul)}</p>` : ''}
                            <p class="text-sm text-gray-500">${escapeHtml(data.pengarang || '-')} • ${escapeHtml(data.penerbit || '-')} • ${escapeHtml(data.tahun_terbit || '-')}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="text-gray-500">Barcode:</span> <span class="font-mono">${escapeHtml(data.barcode || '-')}</span></div>
                            <div><span class="text-gray-500">RFID:</span> <span class="font-mono">${escapeHtml(data.rfid || '-')}</span></div>
                            <div><span class="text-gray-500">ISBN:</span> <span class="font-mono">${escapeHtml(data.isbn || '-')}</span></div>
                            <div><span class="text-gray-500">ISSN:</span> <span class="font-mono">${escapeHtml(data.issn || '-')}</span></div>
                            <div><span class="text-gray-500">Kategori Koleksi:</span> <span class="font-medium">${kategoriKoleksiLabel}</span></div>
                            <div><span class="text-gray-500">Kategori Buku:</span> <span class="font-medium">${escapeHtml(data.kategori?.nama || '-')}</span></div>
                            <div><span class="text-gray-500">Lokasi:</span> <span class="font-medium">${escapeHtml(data.lokasi || 'Ruang Baca Umum')}</span></div>
                            <div><span class="text-gray-500">Rak:</span> <span class="font-medium">${escapeHtml(data.rak || '-')}</span></div>
                            <div><span class="text-gray-500">Format:</span> <span class="font-medium">${escapeHtml(data.format || '-')}</span></div>
                            <div><span class="text-gray-500">Bahasa:</span> <span class="font-medium">${escapeHtml(data.bahasa || '-')}</span></div>
                            <div><span class="text-gray-500">Halaman:</span> <span class="font-medium">${escapeHtml(data.jumlah_halaman || '-')}</span></div>
                            <div><span class="text-gray-500">Dimensi:</span> <span class="font-medium">${escapeHtml(data.ukuran || '-')}</span></div>
                            <div><span class="text-gray-500">Denda/hari:</span> <span class="font-medium">Rp ${escapeHtml(data.denda_per_hari || 500)}</span></div>
                            <div><span class="text-gray-500">Stok:</span> <span class="font-medium">${data.stok_tersedia}/${data.stok}</span></div>
                            <div><span class="text-gray-500">Status:</span> ${statusHtml}</div>
                            <div class="col-span-2"><span class="text-gray-500">Tanggal Pengadaan:</span> <span class="font-medium ${!data.tanggal_pengadaan ? 'text-red-500' : ''}">${formatDate(data.tanggal_pengadaan)}</span></div>
                            <div class="col-span-2"><span class="text-gray-500">Sumber:</span> <span class="font-medium">${escapeHtml(sumberText)}</span></div>
                        </div>
                        
                        ${data.deskripsi ? `
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Deskripsi:</p>
                            <p class="text-sm text-gray-700">${escapeHtml(data.deskripsi)}</p>
                        </div>
                        ` : ''}
                        
                        ${data.kata_kunci ? `
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Kata Kunci:</p>
                            <p class="text-sm text-gray-700">${escapeHtml(data.kata_kunci)}</p>
                        </div>
                        ` : ''}
                        
                        <div class="pt-3 flex gap-2">
                            <a href="/petugas/buku/${data.id}/edit" class="flex-1 bg-indigo-600 text-white text-center px-4 py-2 rounded-lg hover:bg-indigo-700">
                                Edit Buku
                            </a>
                            <button onclick="closeModal()" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-red-600">Gagal memuat detail buku</p>
                    <button onclick="closeModal()" class="mt-3 px-4 py-2 bg-gray-100 rounded-lg">Tutup</button>
                </div>
            `;
        });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function closeModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

document.getElementById('importForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('importSubmitBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

@if(session('import_success_count') && session('import_success_count') > 0)
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: '✨ Import Berhasil!',
        html: '<p><strong>{{ session('import_success_count') }} buku</strong> berhasil diimport ke sistem.</p><div class="bg-yellow-50 p-3 rounded-lg mt-3 text-left"><p class="text-sm text-yellow-800">📷 Jangan lupa tambahkan gambar cover!</p></div>',
        icon: 'success',
        confirmButtonColor: '#4F46E5',
        confirmButtonText: 'OK'
    });
});
@endif
</script>
@endpush

@push('styles')
<style>
[x-cloak] { display: none !important; }
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }
</style>
@endpush
