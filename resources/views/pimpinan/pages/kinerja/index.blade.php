@extends('pimpinan.layouts.app')

@section('title', 'Kinerja & KPI')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Kinerja & KPI
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Monitoring indikator kinerja utama perpustakaan
            </p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">📊 Kunjungan</h3>
                <span class="text-2xl font-bold text-blue-600">{{ $realisasiKunjunganPerHari > $targetKunjunganPerHari ? '✅' : '⚠️' }}</span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Target</span>
                    <span class="font-medium">{{ $targetKunjunganPerHari }}/hari</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Realisasi</span>
                    <span class="font-medium">{{ $realisasiKunjunganPerHari }}/hari</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    @php $persenKunjungan = min(($realisasiKunjunganPerHari / $targetKunjunganPerHari) * 100, 100); @endphp
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $persenKunjungan }}%"></div>
                </div>
                @if($realisasiKunjunganPerHari >= $targetKunjunganPerHari)
                    <p class="text-xs text-green-600 mt-2">✅ Melebihi target</p>
                @else
                    <p class="text-xs text-yellow-600 mt-2">⚠️ Belum mencapai target ({{ round($persenKunjungan) }}%)</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">📚 Peminjaman</h3>
                <span class="text-2xl font-bold text-blue-600">{{ $realisasiPeminjamanPerBulan > $targetPeminjamanPerBulan ? '✅' : '⚠️' }}</span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Target</span>
                    <span class="font-medium">{{ $targetPeminjamanPerBulan }}/bulan</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Realisasi</span>
                    <span class="font-medium">{{ $realisasiPeminjamanPerBulan }}/bulan</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    @php $persenPeminjaman = min(($realisasiPeminjamanPerBulan / $targetPeminjamanPerBulan) * 100, 100); @endphp
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $persenPeminjaman }}%"></div>
                </div>
                @if($realisasiPeminjamanPerBulan >= $targetPeminjamanPerBulan)
                    <p class="text-xs text-green-600 mt-2">✅ Melebihi target</p>
                @else
                    <p class="text-xs text-yellow-600 mt-2">⚠️ Belum mencapai target ({{ round($persenPeminjaman) }}%)</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800">⏱️ Ketepatan Waktu</h3>
                <span class="text-2xl font-bold text-yellow-600">{{ $realisasiKetepatan >= $targetKetepatanWaktu ? '✅' : '⚠️' }}</span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Target</span>
                    <span class="font-medium">{{ $targetKetepatanWaktu }}%</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Realisasi</span>
                    <span class="font-medium">{{ $realisasiKetepatan }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    @php $persenKetepatan = min(($realisasiKetepatan / $targetKetepatanWaktu) * 100, 100); @endphp
                    <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $persenKetepatan }}%"></div>
                </div>
                @if($realisasiKetepatan >= $targetKetepatanWaktu)
                    <p class="text-xs text-green-600 mt-2">✅ Mencapai target</p>
                @else
                    <p class="text-xs text-red-600 mt-2">⚠️ Belum mencapai target ({{ round($persenKetepatan) }}%)</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Ranking Kinerja Petugas --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">🏆 Ranking Kinerja Petugas</h3>
            <p class="text-xs text-gray-500 mt-1">Berdasarkan skor kinerja tahun {{ now()->year }}</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center w-16">Rank</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-center">Transaksi</th>
                        <th class="px-4 py-3 text-center">Denda Diproses</th>
                        <th class="px-4 py-3 text-center">Verifikasi</th>
                        <th class="px-4 py-3 text-center">Skor</th>
                        <th class="px-4 py-3 text-center">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($petugas as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center font-bold">
                            @if($index == 0)
                                <span class="text-2xl">🥇</span>
                            @elseif($index == 1)
                                <span class="text-2xl">🥈</span>
                            @elseif($index == 2)
                                <span class="text-2xl">🥉</span>
                            @else
                                <span class="text-gray-500">#{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($p->total_transaksi) }}</td>
                        <td class="px-4 py-3 text-center">Rp {{ number_format($p->total_denda, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-{{ $p->persen_verifikasi >= 90 ? 'green' : ($p->persen_verifikasi >= 75 ? 'yellow' : 'red') }}-500 h-1.5 rounded-full" style="width: {{ $p->persen_verifikasi }}%"></div>
                                </div>
                                <span class="text-xs font-medium">{{ $p->persen_verifikasi }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-bold">{{ $p->skor }}</td>
                        <td class="px-4 py-3 text-center">{{ $p->rating_star }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada data petugas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabel Tren KPI per Bulan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">📈 Tren KPI per Bulan</h3>
            <p class="text-xs text-gray-500 mt-1">Data 6 bulan terakhir</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Bulan</th>
                        <th class="px-4 py-3 text-center">Kunjungan</th>
                        <th class="px-4 py-3 text-center">Peminjaman</th>
                        <th class="px-4 py-3 text-center">Ketepatan Waktu</th>
                        <th class="px-4 py-3 text-center">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($bulanLabels as $index => $bulan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $bulan }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kpiKunjungan[$index] ?? 0) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kpiPeminjaman[$index] ?? 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs
                                @if(($kpiKetepatan[$index] ?? 0) >= 90) bg-green-100 text-green-700
                                @elseif(($kpiKetepatan[$index] ?? 0) >= 75) bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $kpiKetepatan[$index] ?? 0 }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $prev = $kpiKunjungan[$index-1] ?? null;
                                $curr = $kpiKunjungan[$index] ?? 0;
                                if ($prev && $prev > 0) {
                                    $diff = round((($curr - $prev) / $prev) * 100, 1);
                                    if ($diff > 0) echo "<span class='text-green-600'>↑ {$diff}%</span>";
                                    elseif ($diff < 0) echo "<span class='text-red-600'>↓ " . abs($diff) . "%</span>";
                                    else echo "<span class='text-gray-400'>→ 0%</span>";
                                } else {
                                    echo "<span class='text-gray-400'>-</span>";
                                }
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-4 py-3">Rata-rata</td>
                        <td class="px-4 py-3 text-center">{{ number_format(collect($kpiKunjungan)->avg(), 1) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format(collect($kpiPeminjaman)->avg(), 1) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format(collect($kpiKetepatan)->avg(), 1) }}%</td>
                        <td class="px-4 py-3 text-center">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Rekomendasi Peningkatan --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
        <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Rekomendasi Peningkatan
        </h3>
        <ul class="space-y-2 text-sm text-blue-800">
            @forelse($rekomendasi as $r)
            <li class="flex items-start gap-2">
                <span class="text-blue-500">•</span>
                <span>{{ $r['message'] }}</span>
            </li>
            @empty
            <li class="flex items-start gap-2">
                <span class="text-blue-500">•</span>
                <span>Semua kinerja sudah baik, pertahankan!</span>
            </li>
            @endforelse
        </ul>
    </div>

</div>
@endsection

@push('scripts')
@endpush