@extends('petugas.layouts.app')

@section('title', 'Laporan Denda')

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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">💰 Laporan Denda</h1>
                <p class="text-sm text-gray-500 mt-1">Rekap denda keterlambatan pengembalian yang sudah dibayar</p>
            </div>
        </div>
    </div>

    {{-- Filter dan Export --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <form method="GET" class="flex flex-wrap gap-3">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Dari Tanggal Bayar</label>
                    <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal Bayar</label>
                    <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Tampilkan</button>
                    <a href="{{ route('petugas.report.denda') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">Reset</a>
                </div>
            </form>
            
            {{-- Tombol Export --}}
            <div class="flex gap-2">
                <a href="{{ route('petugas.report.denda.export.excel', request()->all()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('petugas.report.denda.export.pdf', request()->all()) }}" 
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
        $totalDenda = $denda->sum('jumlah_denda');
        $totalTransaksi = $denda->count();
        $rataDenda = $totalTransaksi > 0 ? $totalDenda / $totalTransaksi : 0;
        $maksDenda = $denda->max('jumlah_denda');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Total Denda Dibayar</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ $totalTransaksi }}</p>
            <p class="text-xs text-gray-600">Jumlah Transaksi</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">Rp {{ number_format($rataDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Rata-rata Denda</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($maksDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-600">Denda Tertinggi</p>
        </div>
    </div>

    {{-- Tabel Denda --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left w-12">No</th>
                        <th class="px-4 py-3 text-left">Tanggal Bayar</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-left">Tgl Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left">Tgl Kembali</th>
                        <th class="px-4 py-3 text-left">Keterlambatan</th>
                        <th class="px-4 py-3 text-left">Denda</th>
                        <th class="px-4 py-3 text-left">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($denda as $index => $item)
                    @php
                        // Ambil data dari relasi
                        $peminjaman = $item->peminjaman;
                        $anggota = $item->anggota;
                        $buku = $peminjaman ? $peminjaman->buku : null;
                        
                        // Hitung keterlambatan
                        $terlambat = 0;
                        if ($peminjaman && $peminjaman->tgl_jatuh_tempo && $peminjaman->tanggal_pengembalian) {
                            $jatuhTempo = \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo);
                            $kembali = \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian);
                            $terlambat = $kembali->gt($jatuhTempo) ? $jatuhTempo->diffInDays($kembali) : 0;
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $item->paid_at ? \Carbon\Carbon::parse($item->paid_at)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">
                            <div class="font-medium">{{ $anggota->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $anggota->no_anggota ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-2">
                            <div class="font-medium">{{ $buku->judul ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $buku->pengarang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $peminjaman ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">{{ $peminjaman ? \Carbon\Carbon::parse($peminjaman->tgl_jatuh_tempo)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2">{{ $peminjaman ? \Carbon\Carbon::parse($peminjaman->tanggal_pengembalian)->format('d/m/Y') : '-' }}</td>
                        <td class="px-4 py-2 text-center">{{ $terlambat }} hari</td>
                        <td class="px-4 py-2 font-medium text-red-600">Rp {{ number_format($item->jumlah_denda, 0, ',', '.') }}</td>
                        <td class="px-4 py-2">
                            @if($item->payment_method == 'qris')
                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">QRIS</span>
                            @elseif($item->payment_method == 'tunai')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Tunai</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ $item->payment_method ?? '-' }}</span>
                            @endif
                        </td>
                    <tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p>Belum ada data pembayaran denda</p>
                            <p class="text-sm text-gray-400 mt-1">Denda yang sudah dibayar akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($totalDenda > 0)
                <tfoot class="bg-gray-50 border-t">
                    <tr class="font-bold">
                        <td colspan="8" class="px-4 py-3 text-right">TOTAL DENDA:</td>
                        <td class="px-4 py-3 text-red-600">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection