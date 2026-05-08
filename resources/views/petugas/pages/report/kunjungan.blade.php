@extends('petugas.layouts.app')

@section('title', 'Laporan Kunjungan')

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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">🏛️ Laporan Kunjungan</h1>
                <p class="text-sm text-gray-500 mt-1">Statistik pengunjung perpustakaan</p>
            </div>
        </div>
    </div>

    {{-- Filter dan Export --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <form method="GET" class="flex gap-3">
                <div>
                    <select name="year" class="px-3 py-2 border border-gray-200 rounded-lg">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Tampilkan</button>
            </form>
            
            {{-- Tombol Export --}}
            <div class="flex gap-2">
                <a href="{{ route('petugas.report.kunjungan.export.excel', request()->all()) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
                <a href="{{ route('petugas.report.kunjungan.export.pdf', request()->all()) }}" 
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
        $totalKunjungan = $kunjungan->sum('total');
        $rataBulanan = $kunjungan->avg('total');
        $bulanTertinggi = $kunjungan->sortByDesc('total')->first();
        $bulanTerendah = $kunjungan->sortBy('total')->first();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
            <p class="text-2xl font-bold text-indigo-600">{{ $totalKunjungan }}</p>
            <p class="text-xs text-gray-600">Total Kunjungan {{ request('year', now()->year) }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-xl border border-green-100">
            <p class="text-2xl font-bold text-green-600">{{ number_format($rataBulanan, 0) }}</p>
            <p class="text-xs text-gray-600">Rata-rata per Bulan</p>
        </div>
        @if($bulanTertinggi)
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <p class="text-2xl font-bold text-yellow-600">{{ $bulanTertinggi->total }}</p>
            <p class="text-xs text-gray-600">Tertinggi ({{ $bulanTertinggi->nama_bulan }})</p>
        </div>
        @endif
        @if($bulanTerendah)
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <p class="text-2xl font-bold text-gray-600">{{ $bulanTerendah->total }}</p>
            <p class="text-xs text-gray-600">Terendah ({{ $bulanTerendah->nama_bulan }})</p>
        </div>
        @endif
    </div>

    {{-- Grafik --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Grafik Kunjungan Tahun {{ request('year', now()->year) }}</h3>
        <canvas id="chartKunjungan" height="100"></canvas>
    </div>

    {{-- Tabel Kunjungan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-left">Jumlah Kunjungan</th>
                        <th class="px-4 py-3 text-left">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($kunjungan as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $item->nama_bulan }}</td>
                        <td class="px-4 py-2 font-medium">{{ $item->total }} kunjungan</td>
                        <td class="px-4 py-2">
                            @php $persen = $totalKunjungan > 0 ? round(($item->total / $totalKunjungan) * 100, 1) : 0; @endphp
                            <div class="flex items-center gap-2">
                                <span>{{ $persen }}%</span>
                                <div class="w-32 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartKunjungan').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($kunjungan->pluck('nama_bulan')) !!},
            datasets: [{
                label: 'Jumlah Kunjungan',
                data: {!! json_encode($kunjungan->pluck('total')) !!},
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush