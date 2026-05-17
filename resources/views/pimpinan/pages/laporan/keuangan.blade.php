@extends('pimpinan.layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Laporan Keuangan
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Rekap penerimaan denda perpustakaan
            </p>
        </div>
        
        <div class="flex gap-2 mt-4 md:mt-0">
            <form method="GET" class="flex gap-2">
                <select name="tahun" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm">
                    @for($i = now()->year; $i >= now()->year - 5; $i--)
                    <option value="{{ $i }}" {{ ($tahun ?? now()->year) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition-colors">
                    Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Statistik Keuangan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-green-100 text-sm">Total Denda Tahun {{ $tahun }}</p>
            <p class="text-3xl font-bold">Rp {{ number_format($totalDendaTahun ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-green-100 mt-2">Telah diverifikasi</p>
        </div>
        
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-blue-100 text-sm">Denda Bulan Ini</p>
            <p class="text-3xl font-bold">Rp {{ number_format($dendaBulanIni ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-blue-100 mt-2">{{ now()->format('F Y') }}</p>
        </div>
        
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-yellow-100 text-sm">Rata-rata per Transaksi</p>
            <p class="text-3xl font-bold">Rp {{ number_format($rataDenda ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-yellow-100 mt-2">Per transaksi denda</p>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <p class="text-purple-100 text-sm">Denda Pending</p>
            <p class="text-3xl font-bold">Rp {{ number_format($dendaPendingTotal ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-purple-100 mt-2">Menunggu verifikasi</p>
        </div>
    </div>

    {{-- Rekap Denda per Bulan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-800">📊 Rekap Denda per Bulan ({{ $tahun }})</h3>
            <p class="text-xs text-gray-500 mt-1">Detail denda terlambat dan rusak per bulan</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-center">Transaksi Denda</th>
                        <th class="px-4 py-3 text-center">Denda Terlambat</th>
                        <th class="px-4 py-3 text-center">Denda Rusak</th>
                        <th class="px-4 py-3 text-center">Total Denda</th>
                        <th class="px-4 py-3 text-center">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dendaBulanan ?? [] as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $item->bulan }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->transaksi) }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($item->denda_terlambat, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($item->denda_rusak, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-medium text-red-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-1.5">
                                    @php 
                                        $verifClass = ($item->verifikasi ?? 0) >= 90 ? 'bg-green-500' : (($item->verifikasi ?? 0) >= 75 ? 'bg-yellow-500' : 'bg-red-500');
                                    @endphp
                                    <div class="{{ $verifClass }} h-1.5 rounded-full" style="width: {{ $item->verifikasi ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs">{{ $item->verifikasi ?? 0 }}%</span>
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
                @if(isset($dendaBulanan) && count($dendaBulanan) > 0)
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ number_format(collect($dendaBulanan)->sum('transaksi')) }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('denda_terlambat'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('denda_rusak'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format(collect($dendaBulanan)->sum('total'), 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">-</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Ringkasan Verifikasi Denda --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-green-50 rounded-xl p-5 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600">✅ Disetujui</p>
                    <p class="text-2xl font-bold text-green-700">Rp {{ number_format($totalDisetujui ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-xl font-bold text-green-600">{{ $persenDisetujui ?? 0 }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $persenDisetujui ?? 0 }}%"></div>
            </div>
        </div>
        
        <div class="bg-yellow-50 rounded-xl p-5 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600">⏳ Pending</p>
                    <p class="text-2xl font-bold text-yellow-700">Rp {{ number_format($totalPending ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <span class="text-xl font-bold text-yellow-600">{{ $persenPending ?? 0 }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $persenPending ?? 0 }}%"></div>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-xl p-5 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600">❌ Ditolak</p>
                    <p class="text-2xl font-bold text-red-700">Rp {{ number_format($totalDitolak ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <span class="text-xl font-bold text-red-600">{{ $persenDitolak ?? 0 }}%</span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $persenDitolak ?? 0 }}%"></div>
            </div>
        </div>
    </div>

    {{-- Komposisi Denda --}}
    @if(isset($persenTerlambat) && isset($persenRusak))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">🥧 Komposisi Denda</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="w-32 h-32 mx-auto relative">
                    <svg viewBox="0 0 100 100" class="transform -rotate-90">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#E5E7EB" stroke-width="10"/>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#F97316" stroke-width="10"
                                stroke-dasharray="{{ ($persenTerlambat ?? 0) * 2.827 }} 283"
                                stroke-dashoffset="0"/>
                        <circle cx="50" cy="50" r="45" fill="none" stroke="#10B981" stroke-width="10"
                                stroke-dasharray="{{ ($persenRusak ?? 0) * 2.827 }} 283"
                                stroke-dashoffset="{{ -($persenTerlambat ?? 0) * 2.827 }}"/>
                    </svg>
                </div>
                <div class="mt-3">
                    <div class="flex justify-center gap-4 text-sm">
                        <div class="flex items-center gap-1">
                            <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                            <span>Terlambat: {{ $persenTerlambat ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span>Rusak: {{ $persenRusak ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Denda Terlambat</span>
                            <span>Rp {{ number_format($totalDendaTerlambat ?? 0, 0, ',', '.') }} ({{ $persenTerlambat ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $persenTerlambat ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Denda Rusak</span>
                            <span>Rp {{ number_format($totalDendaRusak ?? 0, 0, ',', '.') }} ({{ $persenRusak ?? 0 }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $persenRusak ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection