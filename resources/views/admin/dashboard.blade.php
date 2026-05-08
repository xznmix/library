@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    .dashboard-card {
        transition: all 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<div class="container-fluid animate-fade-in">
    <!-- Header -->
    <div class="row mb-8">
        <div class="col-12">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-hitam-800 dark:text-white mb-2">Dashboard Admin</h1>
                    <p class="text-muted flex items-center gap-2">
                        <i class="fas fa-user-circle text-oren-500"></i>
                        Selamat datang kembali, <span class="font-semibold text-biru-600 dark:text-biru-400">{{ auth()->user()->name }}!</span>
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="text-sm text-hitam-600 dark:text-gray-400 bg-white dark:bg-hitam-800 px-4 py-2 rounded-lg shadow-sm flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i>
                        {{ now()->translatedFormat('l, d F Y') }}
                        <span class="w-1.5 h-1.5 bg-hijau-500 rounded-full ml-2"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pengguna -->
        <div class="dashboard-card bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-hitam-600 dark:text-gray-400 mb-1">Total Pengguna</p>
                    <h3 class="text-3xl font-bold text-hitam-800 dark:text-white">{{ number_format($totalUsers) }}</h3>
                    <p class="text-xs text-hijau-600 dark:text-hijau-400 mt-2">
                        <i class="fas fa-users mr-1"></i>
                        {{ $totalUsers > 0 ? round(($totalAnggota/$totalUsers)*100, 1) : 0 }}% anggota
                    </p>
                </div>
                <div class="p-3 bg-biru-100 dark:bg-biru-900/30 rounded-xl">
                    <i class="fas fa-users text-2xl text-biru-600 dark:text-biru-400"></i>
                </div>
            </div>
        </div>

        <!-- Total Anggota -->
        <div class="dashboard-card bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-hitam-600 dark:text-gray-400 mb-1">Total Anggota</p>
                    <h3 class="text-3xl font-bold text-hitam-800 dark:text-white">{{ number_format($totalAnggota) }}</h3>
                    <p class="text-xs text-biru-600 dark:text-biru-400 mt-2">
                        <i class="fas fa-user-graduate mr-1"></i>
                        {{ $totalAnggota - ($totalPetugas + $totalAdmin) }} non-staff
                    </p>
                </div>
                <div class="p-3 bg-hijau-100 dark:bg-hijau-900/30 rounded-xl">
                    <i class="fas fa-user-graduate text-2xl text-hijau-600 dark:text-hijau-400"></i>
                </div>
            </div>
        </div>

        <!-- Petugas Perpustakaan -->
        <div class="dashboard-card bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-hitam-600 dark:text-gray-400 mb-1">Petugas</p>
                    <h3 class="text-3xl font-bold text-hitam-800 dark:text-white">{{ number_format($totalPetugas) }}</h3>
                    <p class="text-xs text-biru-600 dark:text-biru-400 mt-2">
                        <i class="fas fa-user-tie mr-1"></i>
                        Operator perpustakaan
                    </p>
                </div>
                <div class="p-3 bg-biru-100 dark:bg-biru-900/30 rounded-xl">
                    <i class="fas fa-user-tie text-2xl text-biru-600 dark:text-biru-400"></i>
                </div>
            </div>
        </div>

        <!-- Administrator -->
        <div class="dashboard-card bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-hitam-600 dark:text-gray-400 mb-1">Administrator</p>
                    <h3 class="text-3xl font-bold text-hitam-800 dark:text-white">{{ number_format($totalAdmin) }}</h3>
                    <p class="text-xs text-oren-600 dark:text-oren-400 mt-2">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Super user
                    </p>
                </div>
                <div class="p-3 bg-oren-100 dark:bg-oren-900/30 rounded-xl">
                    <i class="fas fa-user-shield text-2xl text-oren-600 dark:text-oren-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Ringkasan Sistem -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart Pendaftaran -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6 h-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-hitam-800 dark:text-white">
                        <i class="fas fa-chart-line text-biru-500 mr-2"></i>
                        Tren Pendaftaran Pengguna
                    </h3>
                    <span class="text-sm bg-biru-50 dark:bg-biru-900/30 text-biru-700 dark:text-biru-300 px-3 py-1 rounded-full">
                        Total: {{ array_sum($chartData['data']) }} baru
                    </span>
                </div>
                
                <!-- Grafik Batang Sederhana dengan CSS -->
                <div class="space-y-4">
                    @forelse($chartData['data'] as $index => $value)
                        @php
                            $maxValue = max($chartData['data']) > 0 ? max($chartData['data']) : 1;
                            $percentage = ($value / $maxValue) * 100;
                            $label = $chartData['labels'][$index] ?? 'Bulan ' . ($index + 1);
                            
                            // Warna berbeda untuk setiap bulan: Biru, Hijau, Oren
                            $colors = ['bg-biru-500', 'bg-hijau-500', 'bg-oren-500', 'bg-biru-400', 'bg-hijau-400', 'bg-oren-400'];
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="w-24 text-sm font-medium text-hitam-600 dark:text-gray-400">
                                {{ $label }}
                            </div>
                            <div class="flex-1">
                                <div class="relative pt-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex-1">
                                            <div class="overflow-hidden h-8 text-xs flex rounded-lg bg-gray-200 dark:bg-hitam-700">
                                                <div style="width: {{ $percentage }}%" 
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $color }} transition-all duration-500">
                                                    <span class="text-xs font-bold px-2">
                                                        {{ $value }} orang
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-hitam-500">
                            <i class="fas fa-chart-line text-4xl mb-3 opacity-50"></i>
                            <p>Belum ada data pendaftaran</p>
                        </div>
                    @endforelse
                </div>
                
                <!-- Info tambahan -->
                @if(array_sum($chartData['data']) > 0)
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-hitam-700">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-center p-2 bg-gray-50 dark:bg-hitam-900 rounded-lg">
                            <p class="text-hitam-500 dark:text-gray-400">Rata-rata per bulan</p>
                            <p class="text-xl font-bold text-biru-600 dark:text-biru-400">
                                {{ round(array_sum($chartData['data']) / count($chartData['data'])) }} orang
                            </p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 dark:bg-hitam-900 rounded-lg">
                            <p class="text-hitam-500 dark:text-gray-400">Tertinggi</p>
                            <p class="text-xl font-bold text-hijau-600 dark:text-hijau-400">
                                {{ max($chartData['data']) }} orang
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Ringkasan Sistem -->
        <div>
            <div class="bg-white dark:bg-hitam-800 rounded-xl shadow-sm border border-gray-200 dark:border-hitam-700 p-6 h-full">
                <h3 class="text-lg font-semibold text-hitam-800 dark:text-white mb-6 flex items-center gap-2">
                    <i class="fas fa-server text-biru-500"></i>
                    Ringkasan Sistem
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-hitam-900 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-biru-100 dark:bg-biru-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-biru-600 dark:text-biru-400"></i>
                            </div>
                            <span class="text-sm font-medium text-hitam-700 dark:text-gray-300">Total Buku</span>
                        </div>
                        <span class="text-lg font-semibold text-hitam-800 dark:text-white">{{ number_format($totalBuku) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-hitam-900 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-hijau-100 dark:bg-hijau-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exchange-alt text-hijau-600 dark:text-hijau-400"></i>
                            </div>
                            <span class="text-sm font-medium text-hitam-700 dark:text-gray-300">Peminjaman Aktif</span>
                        </div>
                        <span class="text-lg font-semibold text-hitam-800 dark:text-white">{{ number_format($peminjamanAktif) }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-hitam-900 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-oren-100 dark:bg-oren-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-oren-600 dark:text-oren-400"></i>
                            </div>
                            <span class="text-sm font-medium text-hitam-700 dark:text-gray-300">Terlambat</span>
                        </div>
                        <span class="text-lg font-semibold text-hitam-800 dark:text-white">{{ number_format($peminjamanTerlambat) }}</span>
                    </div>
                </div>

                <!-- Status Sistem -->
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-hitam-700">
                    <h4 class="text-sm font-semibold text-hitam-700 dark:text-gray-300 mb-3">Status Layanan</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-hijau-50 dark:bg-hijau-900/20 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-hitam-500 dark:text-gray-400">Server</span>
                                <span class="w-2 h-2 bg-hijau-500 rounded-full animate-pulse"></span>
                            </div>
                            <p class="text-sm font-medium text-hijau-700 dark:text-hijau-400">Online</p>
                        </div>
                        
                        <div class="bg-hijau-50 dark:bg-hijau-900/20 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-hitam-500 dark:text-gray-400">Database</span>
                                <span class="w-2 h-2 bg-hijau-500 rounded-full"></span>
                            </div>
                            <p class="text-sm font-medium text-hijau-700 dark:text-hijau-400">Connected</p>
                        </div>
                        
                        <div class="col-span-2 bg-gray-50 dark:bg-hitam-900 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-hitam-500 dark:text-gray-400">Update Terakhir</span>
                                <span class="text-sm font-medium text-hitam-700 dark:text-gray-300">{{ $systemStatus['last_update'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let autoRefreshInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const chartLabels = @json($chartData['labels']);
    const chartData = @json($chartData['data']);
    
    // Inisialisasi Chart dengan warna Biru
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Jumlah Pendaftar',
                data: chartData,
                backgroundColor: 'rgba(59, 130, 246, 0.3)',
                borderColor: '#3B82F6',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' orang';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value + ' orang';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush