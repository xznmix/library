@extends('kepala-pustaka.layouts.app')

@section('title', 'Stock Opname PRO - Manajemen Stok Buku')

@section('content')
<div class="space-y-6" x-data="stockOpnameApp()" x-init="init()">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Stock Opname PRO</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Audit stok dengan preview real-time & manajemen inventaris</p>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('kepala-pustaka.audit.buku') }}" 
               class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
            <button @click="refreshData" 
                    class="px-4 py-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Buku</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format(count($bukuList ?? [])) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Opname</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format(count($historyOpname ?? [])) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Selisih</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($historyOpname->sum('selisih') ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Opname</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-white truncate">
                        {{ $historyOpname->first()?->created_at?->diffForHumans() ?? 'Belum pernah' }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Form Stock Opname
            </h3>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('kepala-pustaka.audit.stock-opname-page') }}" id="stockOpnameForm">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📚 Pilih Buku <span class="text-red-500">*</span>
                        </label>
                        <select id="bukuSelect" name="buku_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500" required>
                            <option value="" data-stok="0">-- Pilih Buku --</option>
                            @foreach($bukuList as $buku)
                                <option value="{{ $buku->id }}" data-stok="{{ $buku->stok_tersedia }}">
                                    {{ $buku->judul }} (Stok: {{ $buku->stok_tersedia }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            💻 Stok Sistem
                        </label>
                        <input type="number" id="stokSistem" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 cursor-not-allowed" readonly>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data dari database sistem</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🔍 Stok Fisik <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stokFisik" name="stok_fisik" min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500" required>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hasil pengecekan fisik buku</p>
                    </div>
                </div>
                
                <div class="mt-6 p-5 rounded-xl transition-all duration-300" id="previewCard">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Selisih</p>
                                <p class="text-3xl font-bold" id="selisihText">0</p>
                            </div>
                            <div class="w-px h-10 bg-gray-300 dark:bg-gray-600"></div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                                <p class="text-xl font-bold" id="statusText">-</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Preview akan berubah otomatis
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        Simpan Opname
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Bulk Mode Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Bulk Stock Opname
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 mb-1">Mode inventarisasi massal untuk audit tahunan</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">Lakukan opname untuk semua buku sekaligus dengan sistem scan barcode</p>
                </div>
                <button onclick="alert('🚀 Fitur Bulk Stock Opname akan segera hadir!')" 
                        class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Mulai Scan Massal
                </button>
            </div>
        </div>
    </div>
    
    {{-- History Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Riwayat Stock Opname
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($historyOpname ?? []) }} record</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Stok Sistem</th>
                        <th class="px-4 py-3 text-left">Stok Fisik</th>
                        <th class="px-4 py-3 text-left">Selisih</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historyOpname as $log)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">{{ $log->buku->judul ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $log->stok_sistem }}</td>
                        <td class="px-4 py-3">{{ $log->stok_fisik }}</td>
                        <td class="px-4 py-3 font-bold {{ $log->selisih > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $log->selisih > 0 ? '-' : '+' }}{{ $log->selisih }}
                        </td>
                        <td class="px-4 py-3">
                            @if($log->stok_fisik < $log->stok_sistem)
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-xs">Hilang</span>
                            @elseif($log->stok_fisik > $log->stok_sistem)
                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full text-xs">Lebih</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs">Aman</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            Belum ada riwayat stock opname
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function stockOpnameApp() {
    return {
        loading: false,
        
        init() {
            this.initPreview();
            this.initFormSubmit();
        },
        
        initPreview() {
            const bukuSelect = document.getElementById('bukuSelect');
            const stokFisik = document.getElementById('stokFisik');
            
            if (bukuSelect) {
                bukuSelect.addEventListener('change', () => this.updatePreview());
            }
            if (stokFisik) {
                stokFisik.addEventListener('input', () => this.updatePreview());
            }
        },
        
        initFormSubmit() {
            const form = document.getElementById('stockOpnameForm');
            if (form) {
                form.addEventListener('submit', () => {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        `;
                    }
                });
            }
        },
        
        updatePreview() {
            const bukuSelect = document.getElementById('bukuSelect');
            const stokSistemInput = document.getElementById('stokSistem');
            const stokFisikInput = document.getElementById('stokFisik');
            const previewCard = document.getElementById('previewCard');
            const selisihText = document.getElementById('selisihText');
            const statusText = document.getElementById('statusText');
            
            if (!bukuSelect || !stokSistemInput || !stokFisikInput) return;
            
            const selectedOption = bukuSelect.options[bukuSelect.selectedIndex];
            const stokSistem = selectedOption ? parseInt(selectedOption.getAttribute('data-stok')) || 0 : 0;
            const stokFisik = parseInt(stokFisikInput.value) || 0;
            const selisih = Math.abs(stokSistem - stokFisik);
            
            stokSistemInput.value = stokSistem;
            selisihText.innerText = selisih;
            
            if (stokFisik < stokSistem) {
                statusText.innerText = 'HILANG';
                statusText.className = 'text-xl font-bold text-red-600 dark:text-red-400';
                previewCard.className = 'mt-6 p-5 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
            } else if (stokFisik > stokSistem) {
                statusText.innerText = 'LEBIH';
                statusText.className = 'text-xl font-bold text-yellow-600 dark:text-yellow-400';
                previewCard.className = 'mt-6 p-5 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800';
            } else {
                statusText.innerText = 'AMAN';
                statusText.className = 'text-xl font-bold text-green-600 dark:text-green-400';
                previewCard.className = 'mt-6 p-5 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800';
            }
        },
        
        refreshData() {
            this.loading = true;
            setTimeout(() => location.reload(), 500);
        }
    }
}

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    const bukuSelect = document.getElementById('bukuSelect');
    if (bukuSelect && bukuSelect.value) {
        bukuSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection