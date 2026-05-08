@extends('petugas.layouts.app')

@section('title', 'Laporan Peminjaman')

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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">📋 Laporan Peminjaman</h1>
                <p class="text-sm text-gray-500 mt-1">Rekap data peminjaman buku perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('petugas.report.peminjaman') }}" class="flex flex-wrap gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Status</label>
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('petugas.report.peminjaman') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Statistik --}}
    @php
        $totalPinjam = $peminjaman->count();
        $tepatWaktu = $peminjaman->where('status_pinjam', 'dikembalikan')->filter(function($item) {
            $dendaValue = $item->denda_total ?? 0;
            if ($dendaValue == 0 && $item->extra_attributes) {
                $dendaValue = ($item->extra_attributes['denda_terlambat'] ?? 0) + ($item->extra_attributes['denda_rusak'] ?? 0);
            }
            return $dendaValue == 0;
        })->count();
        
        $terlambat = $peminjaman->where('status_pinjam', 'terlambat')->count();
        $masihDipinjam = $peminjaman->where('status_pinjam', 'dipinjam')->count();
        
        // Hitung total denda dari data yang ada
        $totalDendaFromData = 0;
        foreach($peminjaman as $item) {
            $dendaValue = $item->denda_total ?? 0;
            if ($dendaValue == 0 && $item->extra_attributes) {
                $dendaValue = ($item->extra_attributes['denda_terlambat'] ?? 0) + ($item->extra_attributes['denda_rusak'] ?? 0);
            }
            $totalDendaFromData += $dendaValue;
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">{{ $totalPinjam }}</p>
            <p class="text-xs text-gray-600">Total Transaksi</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ $tepatWaktu }}</p>
            <p class="text-xs text-gray-600">Tepat Waktu</p>
        </div>
        <div class="bg-red-50 p-4 rounded-xl border border-red-100">
            <p class="text-2xl font-bold text-red-600">{{ $terlambat }}</p>
            <p class="text-xs text-gray-600">Terlambat</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">{{ $masihDipinjam }}</p>
            <p class="text-xs text-gray-600">Masih Dipinjam</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($totalDendaFromData, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Denda</p>
        </div>
    </div>

    {{-- Tombol Export --}}
    <div class="flex justify-end gap-2 mb-4">
        <a href="{{ route('petugas.report.peminjaman.export.pdf', request()->all()) }}" 
           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('petugas.report.peminjaman.export.excel', request()->all()) }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </a>
    </div>

    {{-- Tabel Peminjaman --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal Pinjam</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Tgl Kembali</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Denda</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($peminjaman as $index => $item)
                    @php
                        // Hitung denda dengan benar
                        $dendaValue = $item->denda_total ?? 0;
                        if ($dendaValue == 0 && $item->extra_attributes) {
                            $dendaValue = ($item->extra_attributes['denda_terlambat'] ?? 0) + ($item->extra_attributes['denda_rusak'] ?? 0);
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $item->user->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->buku->judul ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $item->tanggal_pengembalian ? \Carbon\Carbon::parse($item->tanggal_pengembalian)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">
                            @if($item->status_pinjam == 'dipinjam')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Dipinjam</span>
                            @elseif($item->status_pinjam == 'terlambat')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">Terlambat</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">Kembali</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($dendaValue > 0)
                                <span class="text-red-600 font-medium">Rp {{ number_format($dendaValue, 0, ',', '.') }}</span>
                            @else
                                Rp 0
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data peminjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t">
                    <tr class="font-bold">
                        <td colspan="6" class="px-4 py-3 text-right">TOTAL DENDA:</td>
                        <td class="px-4 py-3 text-red-600">
                            Rp {{ number_format($totalDendaFromData, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection