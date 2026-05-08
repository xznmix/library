@extends('pimpinan.layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard Eksekutif
            </h1>
            <p class="text-gray-500 mt-1">
                Ringkasan kinerja perpustakaan {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-blue-100 text-sm">Total Buku</p>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold">{{ number_format($totalBuku) }}</p>
            <p class="text-xs text-blue-100 mt-2">Total koleksi buku</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-green-100 text-sm">Total Anggota</p>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold">{{ number_format($totalAnggota) }}</p>
            <p class="text-xs text-green-100 mt-2">Total anggota terdaftar</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-purple-100 text-sm">Total Peminjaman</p>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold">{{ number_format($totalPeminjaman) }}</p>
            <p class="text-xs text-purple-100 mt-2">
                @if($persenPeminjaman > 0) ↑ @elseif($persenPeminjaman < 0) ↓ @endif {{ abs($persenPeminjaman) }}% dari tahun lalu
            </p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-yellow-100 text-sm">Total Denda</p>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
            <p class="text-xs text-yellow-100 mt-2">
                @if($persenDenda > 0) ↑ @elseif($persenDenda < 0) ↓ @endif {{ abs($persenDenda) }}% dari tahun lalu
            </p>
        </div>
    </div>

    {{-- KPI Tracker --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Indikator Kinerja Utama (KPI)</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Kunjungan per Hari</span>
                        <span class="font-medium text-gray-800">
                            {{ $realisasiKunjunganPerHari }} / {{ $targetKunjunganPerHari }}
                            @if($realisasiKunjunganPerHari >= $targetKunjunganPerHari)
                                <span class="text-green-600 ml-2">✅</span>
                            @else
                                <span class="text-yellow-600 ml-2">⚠️</span>
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php $persenKunjungan = min(($realisasiKunjunganPerHari / $targetKunjunganPerHari) * 100, 100); @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $persenKunjungan }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Peminjaman per Bulan</span>
                        <span class="font-medium text-gray-800">
                            {{ $realisasiPeminjamanPerBulan }} / {{ $targetPeminjamanPerBulan }}
                            @if($realisasiPeminjamanPerBulan >= $targetPeminjamanPerBulan)
                                <span class="text-green-600 ml-2">✅</span>
                            @else
                                <span class="text-yellow-600 ml-2">⚠️</span>
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php $persenPeminjamanBulan = min(($realisasiPeminjamanPerBulan / $targetPeminjamanPerBulan) * 100, 100); @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $persenPeminjamanBulan }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Ketepatan Waktu</span>
                        <span class="font-medium text-gray-800">
                            {{ $persenTepatWaktu }}% / {{ $targetKetepatanWaktu }}%
                            @if($persenTepatWaktu >= $targetKetepatanWaktu)
                                <span class="text-green-600 ml-2">✅</span>
                            @else
                                <span class="text-yellow-600 ml-2">⚠️</span>
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php $persenKetepatan = min(($persenTepatWaktu / $targetKetepatanWaktu) * 100, 100); @endphp
                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $persenKetepatan }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Keanggotaan Aktif</span>
                        <span class="font-medium text-gray-800">
                            {{ $realisasiKeanggotaanAktif }}% / {{ $targetKeanggotaanAktif }}%
                            @if($realisasiKeanggotaanAktif >= $targetKeanggotaanAktif)
                                <span class="text-green-600 ml-2">✅</span>
                            @else
                                <span class="text-yellow-600 ml-2">⚠️</span>
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php $persenKeanggotaan = min(($realisasiKeanggotaanAktif / $targetKeanggotaanAktif) * 100, 100); @endphp
                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $persenKeanggotaan }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Peringatan Penting</h3>
            <div class="space-y-3">
                @forelse($peringatan as $item)
                <div class="flex items-start gap-3 p-3 
                    @if($item['type'] == 'warning') bg-yellow-50
                    @elseif($item['type'] == 'danger') bg-red-50
                    @else bg-green-50 @endif rounded-lg">
                    <div class="w-8 h-8 rounded-full 
                        @if($item['type'] == 'warning') bg-yellow-100
                        @elseif($item['type'] == 'danger') bg-red-100
                        @else bg-green-100 @endif 
                        flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 
                            @if($item['type'] == 'warning') text-yellow-600
                            @elseif($item['type'] == 'danger') text-red-600
                            @else text-green-600 @endif" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($item['type'] == 'danger')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            @elseif($item['type'] == 'warning')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $item['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $item['message'] }}</p>
                    </div>
                </div>
                @empty
                <div class="flex items-start gap-3 p-3 bg-green-50 rounded-lg">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Semua Berjalan Baik</p>
                        <p class="text-xs text-gray-500">Tidak ada peringatan penting saat ini</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Grafik Tren 6 Bulan (LINE CHART dengan Chart.js) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">📈 Tren Peminjaman & Kunjungan 6 Bulan Terakhir</h3>
        
        @if(empty($trenPeminjaman) || array_sum($trenPeminjaman) == 0)
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-400">Belum ada data peminjaman dan kunjungan</p>
            </div>
        @else
            <div class="relative" style="height: 350px;">
                <canvas id="chartTren6Bulan"></canvas>
            </div>
        @endif
    </div>

    {{-- Grafik Tren 5 Tahun (LINE CHART dengan Chart.js) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h3 class="font-semibold text-gray-800 mb-4">📊 Tren Peminjaman 5 Tahun Terakhir</h3>
        
        @if(empty($tren5TahunPeminjaman) || array_sum($tren5TahunPeminjaman) == 0)
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-400">Belum ada data peminjaman 5 tahun terakhir</p>
            </div>
        @else
            <div class="relative" style="height: 350px;">
                <canvas id="chartTren5Tahun"></canvas>
            </div>
        @endif
    </div>

    {{-- Buku Populer & Anggota Aktif --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Buku Paling Populer</h3>
            <div class="space-y-3">
                @forelse($bukuPopuler as $index => $buku)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $buku->judul }}</p>
                        <p class="text-xs text-gray-500">{{ $buku->pengarang ?? '-' }}</p>
                    </div>
                    <span class="text-sm font-medium text-indigo-600">{{ $buku->peminjaman_count }}x</span>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Belum ada data peminjaman</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Anggota Paling Aktif</h3>
            <div class="space-y-3">
                @forelse($anggotaAktif as $index => $anggota)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $anggota->name }}</p>
                        <p class="text-xs text-gray-500">{{ $anggota->kelas ?? '-' }} • {{ ucfirst($anggota->role ?? 'Anggota') }}</p>
                    </div>
                    <span class="text-sm font-medium text-green-600">{{ $anggota->peminjaman_count }}x</span>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">Belum ada data peminjaman</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // CHART 1: TREN PEMINJAMAN & KUNJUNGAN 6 BULAN (LINE CHART)
    // ============================================================
    @if(!empty($trenPeminjaman) && array_sum($trenPeminjaman) > 0)
    const ctx6Bulan = document.getElementById('chartTren6Bulan').getContext('2d');
    new Chart(ctx6Bulan, {
        type: 'line',
        data: {
            labels: {!! json_encode($bulanLabels) !!},
            datasets: [
                {
                    label: 'Peminjaman',
                    data: {!! json_encode($trenPeminjaman) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Kunjungan',
                    data: {!! json_encode($trenKunjungan) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} kali` } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, title: { display: true, text: 'Jumlah' } },
                x: { grid: { display: false }, title: { display: true, text: 'Bulan' } }
            }
        }
    });
    @endif

    // ============================================================
    // CHART 2: TREN PEMINJAMAN 5 TAHUN (LINE CHART)
    // ============================================================
    @if(!empty($tren5TahunPeminjaman) && array_sum($tren5TahunPeminjaman) > 0)
    const ctx5Tahun = document.getElementById('chartTren5Tahun').getContext('2d');
    new Chart(ctx5Tahun, {
        type: 'line',
        data: {
            labels: {!! json_encode($tahunLabels) !!},
            datasets: [
                {
                    label: 'Peminjaman',
                    data: {!! json_encode($tren5TahunPeminjaman) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#8b5cf6',
                    pointBorderColor: '#fff',
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointStyle: 'circle'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.dataset.label}: ${ctx.raw.toLocaleString()} kali` } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, title: { display: true, text: 'Jumlah Peminjaman' } },
                x: { grid: { display: false }, title: { display: true, text: 'Tahun' } }
            }
        }
    });
    @endif
});
</script>
@endpush