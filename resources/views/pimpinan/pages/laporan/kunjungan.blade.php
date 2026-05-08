@extends('pimpinan.layouts.app')

@section('title', 'Laporan Kunjungan')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Laporan Kunjungan
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Rekap kunjungan perpustakaan
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

    {{-- Statistik Kunjungan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">📊 Total Kunjungan</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($totalKunjungan) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Tahun {{ $tahun }}</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">📅 Rata-rata per Hari</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $rataPerHari }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Sepanjang tahun</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">👨‍🎓 Siswa</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($kunjunganSiswa) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $totalKunjungan > 0 ? round(($kunjunganSiswa / $totalKunjungan) * 100, 1) : 0 }}% dari total</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">👩‍🏫 Guru & Umum</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($kunjunganGuru) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $totalKunjungan > 0 ? round(($kunjunganGuru / $totalKunjungan) * 100, 1) : 0 }}% dari total</p>
        </div>
    </div>

    {{-- Tabel Kunjungan per Jam (Ganti Grafik) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">⏰ Kunjungan per Jam</h3>
                <p class="text-xs text-gray-500 mt-1">Distribusi kunjungan berdasarkan jam</p>
            </div>
            <div class="overflow-x-auto p-4">
                <div class="space-y-2">
                    @foreach($labelsJam as $index => $jam)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $jam }}</span>
                            <span class="font-medium">{{ $dataJam[$index] ?? 0 }} kunjungan</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php $persenJam = max($dataJam) > 0 ? (($dataJam[$index] ?? 0) / max($dataJam)) * 100 : 0; @endphp
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $persenJam }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-800 dark:text-white">📅 Tren Kunjungan Harian (7 Hari Terakhir)</h3>
                <p class="text-xs text-gray-500 mt-1">Data kunjungan 7 hari terakhir</p>
            </div>
            <div class="overflow-x-auto p-4">
                <div class="space-y-2">
                    @foreach($labelsHarian as $index => $hari)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">{{ $hari }}</span>
                            <span class="font-medium">{{ $dataHarian[$index] ?? 0 }} kunjungan</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php $persenHari = max($dataHarian) > 0 ? (($dataHarian[$index] ?? 0) / max($dataHarian)) * 100 : 0; @endphp
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $persenHari }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Rekap Kunjungan per Hari --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">📋 Rekap Kunjungan per Hari</h3>
            <p class="text-xs text-gray-500 mt-1">10 data terbaru</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Siswa</th>
                        <th class="px-4 py-3 text-center">Guru</th>
                        <th class="px-4 py-3 text-center">Pegawai</th>
                        <th class="px-4 py-3 text-center">Umum</th>
                        <th class="px-4 py-3 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($kunjunganHarian as $index => $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->siswa) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->guru) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->pegawai) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($item->umum) }}</td>
                        <td class="px-4 py-3 text-center font-medium">{{ number_format($item->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Belum ada data kunjungan untuk tahun {{ $tahun }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                    <tr>
                        <td colspan="2" class="px-4 py-3">Total</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kunjunganHarian->sum('siswa')) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kunjunganHarian->sum('guru')) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kunjunganHarian->sum('pegawai')) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kunjunganHarian->sum('umum')) }}</td>
                        <td class="px-4 py-3 text-center">{{ number_format($kunjunganHarian->sum('total')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
@endpush