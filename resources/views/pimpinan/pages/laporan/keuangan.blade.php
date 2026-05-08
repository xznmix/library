@extends('pimpinan.layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Laporan Keuangan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Rekap penerimaan denda perpustakaan
            </p>
        </div>
        
        <div class="flex gap-2 mt-4 md:mt-0">
            <div>
                <form method="GET" class="flex gap-2">
                    <select name="tahun" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                        <option value="{{ $i }}" {{ ($tahun ?? now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">Filter</button>
                </form>
            </div>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Statistik Keuangan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-green-100 text-sm">Total Denda Tahun {{ $tahun }}</p>
            <p class="text-3xl font-bold">Rp {{ number_format($totalDendaTahun, 0, ',', '.') }}</p>
            <p class="text-xs text-green-100 mt-2">Telah diverifikasi</p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-blue-100 text-sm">Denda Bulan Ini</p>
            <p class="text-3xl font-bold">Rp {{ number_format($dendaBulanIni, 0, ',', '.') }}</p>
            <p class="text-xs text-blue-100 mt-2">{{ now()->format('F Y') }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-yellow-100 text-sm">Rata-rata per Transaksi</p>
            <p class="text-3xl font-bold">Rp {{ number_format($rataDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-yellow-100 mt-2">Per transaksi denda</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-purple-100 text-sm">Denda Pending</p>
            <p class="text-3xl font-bold">Rp {{ number_format($dendaPendingTotal, 0, ',', '.') }}</p>
            <p class="text-xs text-purple-100 mt-2">Menunggu verifikasi</p>
        </div>
    </div>

    {{-- Tabel Denda per Bulan (Ganti Grafik) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">📊 Rekap Denda per Bulan ({{ $tahun }})</h3>
            <p class="text-xs text-gray-500 mt-1">Detail denda terlambat dan rusak per bulan</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-center">Transaksi Denda</th>
                        <th class="px-4 py-3 text-center">Denda Terlambat</th>
                        <th class="px-4 py-3 text-center">Denda Rusak</th>
                        <th class="px-4 py-3 text-center">Total Denda</th>
                        <th class="px-4 py-3 text-center">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($dendaBulanan as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-medium">{{ $item->bulan }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->transaksi) }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($item->denda_terlambat, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($item->denda_rusak, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-medium">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-{{ $item->verifikasi >= 90 ? 'green' : ($item->verifikasi >= 75 ? 'yellow' : 'red') }}-500 h-1.5 rounded-full" style="width: {{ $item->verifikasi }}%"></div>
                                </div>
                                <span class="text-xs">{{ $item->verifikasi }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Belum ada data denda untuk tahun {{ $tahun }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <tr>
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ number_format(collect($dendaBulanan)->sum('transaksi')) }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('denda_terlambat'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('denda_rusak'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('total'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Ringkasan Verifikasi --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-5 border border-green-200 dark:border-green-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 dark:text-green-400">✅ Disetujui</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-800/30 flex items-center justify-center">
                    <span class="text-xl font-bold text-green-600">{{ $persenDisetujui }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $persenDisetujui }}%"></div>
            </div>
        </div>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-5 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 dark:text-yellow-400">⏳ Pending</p>
                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">Rp {{ number_format($totalPending, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-800/30 flex items-center justify-center">
                    <span class="text-xl font-bold text-yellow-600">{{ $persenPending }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $persenPending }}%"></div>
            </div>
        </div>
        
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-5 border border-red-200 dark:border-red-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 dark:text-red-400">❌ Ditolak</p>
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">Rp {{ number_format($totalDitolak, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-800/30 flex items-center justify-center">
                    <span class="text-xl font-bold text-red-600">{{ $persenDitolak }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $persenDitolak }}%"></div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@endpush