@extends('petugas.layouts.app')

@section('title', 'Laporan Buku')

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('petugas.report.index') }}" 
               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📚 Laporan Buku</h1>
                <p class="text-sm text-gray-500 mt-1">Koleksi dan statistik buku perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Filter dan Export --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <form method="GET" class="flex flex-wrap gap-3">
                <div>
                    <select name="kategori" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\KategoriBuku::all() as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="tipe" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="fisik" {{ request('tipe') == 'fisik' ? 'selected' : '' }}>Fisik</option>
                        <option value="digital" {{ request('tipe') == 'digital' ? 'selected' : '' }}>Digital</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Filter</button>
                <a href="{{ route('petugas.report.buku') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">Reset</a>
            </form>
            
            {{-- Tombol Export --}}
            <div class="flex gap-2">
                <a href="{{ route('petugas.report.buku.export.excel', request()->all()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('petugas.report.buku.export.pdf', request()->all()) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    @php
        $totalBuku = $buku->count();
        $totalFisik = $buku->where('tipe', 'fisik')->count();
        $totalDigital = $buku->where('tipe', 'digital')->count();
        $totalStok = $buku->sum('stok');
        $totalDipinjam = $buku->sum('peminjaman_count');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">{{ $totalBuku }}</p>
            <p class="text-xs text-gray-600">Total Judul</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-2xl font-bold text-blue-600">{{ $totalFisik }}</p>
            <p class="text-xs text-gray-600">Buku Fisik</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
            <p class="text-2xl font-bold text-purple-600">{{ $totalDigital }}</p>
            <p class="text-xs text-gray-600">Buku Digital</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ $totalStok }}</p>
            <p class="text-xs text-gray-600">Total Eksemplar</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">{{ $totalDipinjam }}</p>
            <p class="text-xs text-gray-600">Kali Dipinjam</p>
        </div>
    </div>

    {{-- Tabel Buku --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-left">Pengarang</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-left">Stok</th>
                        <th class="px-4 py-3 text-left">Tersedia</th>
                        <th class="px-4 py-3 text-left">Dipinjam</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($buku as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium">{{ $item->judul }}</td>
                        <td class="px-4 py-2">{{ $item->pengarang ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->kategori->nama ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 {{ $item->tipe == 'digital' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} rounded-full text-xs">
                                {{ ucfirst($item->tipe) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $item->stok }}</td>
                        <td class="px-4 py-2">{{ $item->stok_tersedia }}</td>
                        <td class="px-4 py-2">{{ $item->peminjaman_count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data buku
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection