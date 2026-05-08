@extends('kepala-pustaka.layouts.app')

@section('title', 'Verifikasi Denda')
@section('page-title', 'Verifikasi Denda')

@section('content')
<div class="space-y-6">

    {{-- Header dengan Quick Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Verifikasi Denda</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan verifikasi denda yang dicatat oleh petugas</p>
                </div>
            </div>
        </div>
        
        {{-- Statistik Lengkap --}}
        <div class="flex flex-wrap gap-3">
            <div class="bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200 min-w-[120px] text-center hover:shadow-md transition-shadow">
                <p class="text-xs text-yellow-600 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $statistik['pending'] }}</p>
                <p class="text-xs text-yellow-600 mt-1 font-mono">
                    Rp {{ number_format($statistik['total_nominal_pending'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-green-50 px-4 py-2 rounded-lg border border-green-200 min-w-[120px] text-center hover:shadow-md transition-shadow">
                <p class="text-xs text-green-600 uppercase tracking-wider">Disetujui</p>
                <p class="text-2xl font-bold text-green-700">{{ $statistik['disetujui'] }}</p>
                <p class="text-xs text-green-600 mt-1 font-mono">
                    Rp {{ number_format($statistik['total_nominal_disetujui'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-red-50 px-4 py-2 rounded-lg border border-red-200 min-w-[100px] text-center hover:shadow-md transition-shadow">
                <p class="text-xs text-red-600 uppercase tracking-wider">Ditolak</p>
                <p class="text-2xl font-bold text-red-700">{{ $statistik['ditolak'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Filter Data</h3>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="selectAll" class="text-sm text-gray-700">Pilih Semua</label>
                    </div>
                    
                    <div class="flex gap-2" id="bulkActions" style="display: none;">
                        <span class="text-sm bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full" id="selectedCount">0 dipilih</span>
                        <button onclick="verifikasiMassal('disetujui')" 
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Setujui Massal
                        </button>
                        <button onclick="verifikasiMassal('ditolak')" 
                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Tolak Massal
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Petugas</label>
                    <select name="petugas" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                        <option value="">Semua Petugas</option>
                        @foreach($petugas as $p)
                            <option value="{{ $p->id }}" {{ request('petugas') == $p->id ? 'selected' : '' }}>👤 {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Min. Nominal</label>
                    <input type="number" name="min_nominal" value="{{ request('min_nominal') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                           placeholder="Minimal Rp">
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('kepala-pustaka.verifikasi.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Statistik Per Petugas --}}
    @if(isset($statistikPetugas) && count($statistikPetugas) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="font-semibold text-gray-800">Statistik Verifikasi per Petugas</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($statistikPetugas as $p)
            <div class="bg-gradient-to-br from-gray-50 to-white p-3 rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-sm font-bold text-indigo-600">{{ substr($p->name, 0, 1) }}</span>
                    </div>
                    <p class="font-medium text-gray-800 truncate">{{ $p->name }}</p>
                </div>
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-yellow-600 flex items-center gap-1">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        Pending: {{ $p->denda_pending ?? 0 }}
                    </span>
                    <span class="text-green-600 flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Disetujui: {{ $p->denda_disetujui ?? 0 }}
                    </span>
                </div>
                <div class="mt-2 pt-2 border-t border-gray-100">
                    <p class="text-xs text-gray-500">Total denda disetujui:</p>
                    <p class="text-sm font-bold text-indigo-600">Rp {{ number_format($p->total_nominal ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel Denda --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800">Daftar Denda</h3>
            </div>
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                {{ $dendas->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
                        <th class="px-4 py-3 text-left w-10 rounded-tl-xl">
                            <input type="checkbox" id="selectAllTable" class="w-4 h-4 rounded border-white text-indigo-600">
                        </th>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Petugas</th>
                        <th class="px-4 py-3 text-left">Anggota</th>
                        <th class="px-4 py-3 text-left">Buku</th>
                        <th class="px-4 py-3 text-left">Denda</th>
                        <th class="px-4 py-3 text-left">Terlambat</th>
                        <th class="px-4 py-3 text-left">Kondisi</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dendas as $index => $denda)
                    <tr class="hover:bg-gray-50 transition-colors {{ $denda->status_verifikasi == 'pending' ? 'bg-yellow-50/50' : '' }}">
                        <td class="px-4 py-3">
                            @if($denda->status_verifikasi == 'pending')
                                <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $denda->id }}">
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $dendas->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $denda->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $denda->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $denda->petugas->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $denda->petugas->role ?? 'Petugas' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $denda->user->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $denda->user->no_anggota ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium line-clamp-1">{{ $denda->buku->judul ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $denda->buku->pengarang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono font-bold text-red-600">
                            Rp {{ number_format($denda->denda_total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $jatuhTempo = \Carbon\Carbon::parse($denda->tgl_jatuh_tempo);
                                $kembali = \Carbon\Carbon::parse($denda->tanggal_pengembalian);
                                $terlambat = $kembali->diffInDays($jatuhTempo);
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $terlambat }} hari
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $kondisiClass = match($denda->kondisi_kembali) {
                                    'baik' => 'bg-green-100 text-green-700',
                                    'rusak_ringan' => 'bg-yellow-100 text-yellow-700',
                                    'rusak_berat' => 'bg-orange-100 text-orange-700',
                                    'hilang' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $kondisiText = match($denda->kondisi_kembali) {
                                    'baik' => 'Baik',
                                    'rusak_ringan' => 'Rusak Ringan',
                                    'rusak_berat' => 'Rusak Berat',
                                    'hilang' => 'Hilang',
                                    default => '-'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $kondisiClass }}">
                                {{ $kondisiText }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($denda->status_verifikasi == 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse"></span>
                                    Pending
                                </span>
                            @elseif($denda->status_verifikasi == 'disetujui')
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('kepala-pustaka.verifikasi.detail', $denda->id) }}" 
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors group"
                                   title="Detail">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                
                                @if($denda->status_verifikasi == 'pending')
                                    <button type="button" 
                                            onclick="openVerifikasiModal({{ $denda->id }}, {{ $denda->denda_total }})"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors group"
                                            title="Verifikasi">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-lg">Tidak ada data denda</p>
                                <p class="text-sm text-gray-400 mt-1">Belum ada denda yang perlu diverifikasi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dendas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $dendas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL VERIFIKASI (FIX CENTER + MODERN UI) --}}
<div id="verifikasiModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeVerifikasiModal()"></div>

    <div class="relative w-full max-w-lg mx-auto px-4">
        
        <div id="modalContent"
             class="bg-white rounded-2xl shadow-2xl border border-gray-100 transform transition-all duration-300 scale-95 opacity-0">

            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Verifikasi Denda</h3>
                            <p class="text-sm text-indigo-200">Setujui atau tolak denda ini</p>
                        </div>
                    </div>
                    <button onclick="closeVerifikasiModal()" class="text-white/70 hover:text-white transition">
                        ✕
                    </button>
                </div>
            </div>

            <div class="p-6">
                <form id="verifikasiForm" method="POST">
                    @csrf
                    <input type="hidden" id="verifikasiStatus" name="status">
                    <input type="hidden" id="verifikasiId" name="id">

                    <div class="mb-5">
                        <label class="block text-sm font-medium mb-3">Pilih Keputusan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" id="btnSetujui"
                                onclick="setStatus('disetujui')"
                                class="py-3 rounded-xl border-2 border-green-500 bg-green-50 text-green-700 font-medium hover:scale-105 active:scale-95 transition">
                                ✅ Setujui
                            </button>
                            <button type="button" id="btnTolak"
                                onclick="setStatus('ditolak')"
                                class="py-3 rounded-xl border-2 border-gray-300 hover:border-red-400 hover:bg-red-50 hover:text-red-600 transition">
                                ❌ Tolak
                            </button>
                        </div>
                    </div>

                    <div id="nominalSection">
                        <label class="text-sm font-medium">Nominal Disetujui</label>
                        <div class="relative mt-1">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="number" id="nominalSetuju" name="nominal_setuju"
                                   class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500 transition"
                                   placeholder="0">
                        </div>
                        <p id="infoDendaAsli" class="text-xs text-gray-500 mt-1"></p>
                    </div>

                    <div id="catatanSection" class="hidden mt-4">
                        <label class="text-sm font-medium">Catatan Penolakan <span class="text-red-500">*</span></label>
                        <textarea id="catatan" name="catatan"
                                  class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-200 focus:border-red-500 transition resize-none"
                                  rows="3" placeholder="Tuliskan alasan penolakan..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 border-t pt-4">
                        <button type="button" onclick="closeVerifikasiModal()"
                                class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" id="submitVerifikasiBtn"
                                class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:scale-105 active:scale-95 transition shadow-md">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============================================================
// SELECT ALL CHECKBOX
// ============================================================
let selectedIds = [];

function updateSelectedCount() {
    const count = selectedIds.length;
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActions = document.getElementById('bulkActions');
    
    if (selectedCountSpan) selectedCountSpan.textContent = count + ' dipilih';
    if (bulkActions) bulkActions.style.display = count > 0 ? 'flex' : 'none';
}

function toggleSelectAll() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    const isChecked = (selectAll?.checked || selectAllTable?.checked) || false;
    
    selectedIds = isChecked ? Array.from(checkboxes).map(cb => cb.value) : [];
    checkboxes.forEach(cb => cb.checked = isChecked);
    updateSelectedCount();
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    
    if (selectAll) selectAll.addEventListener('change', toggleSelectAll);
    if (selectAllTable) selectAllTable.addEventListener('change', toggleSelectAll);
    
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            if (this.checked && !selectedIds.includes(this.value)) {
                selectedIds.push(this.value);
            } else if (!this.checked) {
                selectedIds = selectedIds.filter(id => id !== this.value);
            }
            updateSelectedCount();
            
            const allCheckboxes = document.querySelectorAll('.row-checkbox');
            const allChecked = allCheckboxes.length === selectedIds.length && allCheckboxes.length > 0;
            if (selectAll) selectAll.checked = allChecked;
            if (selectAllTable) selectAllTable.checked = allChecked;
        });
    });
});

// ============================================================
// VERIFIKASI MODAL
// ============================================================
let currentDendaId = null;
let currentMaxDenda = 0;

function openVerifikasiModal(id, maxDenda) {
    currentDendaId = id;
    currentMaxDenda = maxDenda;

    const modal = document.getElementById('verifikasiModal');
    const content = document.getElementById('modalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');

    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
        document.getElementById('nominalSetuju').focus();
    }, 10);

    document.getElementById('verifikasiStatus').value = 'disetujui';
    document.getElementById('verifikasiId').value = id;
    document.getElementById('nominalSetuju').value = '';
    document.getElementById('catatan').value = '';

    document.getElementById('infoDendaAsli').innerHTML =
        "💰 Denda asli: Rp " + new Intl.NumberFormat('id-ID').format(maxDenda);

    setStatus('disetujui');
    document.getElementById('verifikasiForm').action = `/kepala-pustaka/verifikasi/${id}`;
}

function closeVerifikasiModal() {
    const modal = document.getElementById('verifikasiModal');
    const content = document.getElementById('modalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }, 200);
}

function setStatus(status) {
    const btnSetujui = document.getElementById('btnSetujui');
    const btnTolak = document.getElementById('btnTolak');
    const nominal = document.getElementById('nominalSection');
    const catatan = document.getElementById('catatanSection');

    if (status === 'disetujui') {
        btnSetujui.className = 'py-3 rounded-xl border-2 border-green-500 bg-green-50 text-green-700 font-medium hover:scale-105 active:scale-95 transition';
        btnTolak.className = 'py-3 rounded-xl border-2 border-gray-300 hover:border-red-400 hover:bg-red-50 hover:text-red-600 transition';
        nominal.classList.remove('hidden');
        catatan.classList.add('hidden');
        document.getElementById('verifikasiStatus').value = 'disetujui';
    } else {
        btnTolak.className = 'py-3 rounded-xl border-2 border-red-500 bg-red-50 text-red-700 font-medium hover:scale-105 active:scale-95 transition';
        btnSetujui.className = 'py-3 rounded-xl border-2 border-gray-300 hover:border-green-400 hover:bg-green-50 hover:text-green-600 transition';
        nominal.classList.add('hidden');
        catatan.classList.remove('hidden');
        document.getElementById('verifikasiStatus').value = 'ditolak';
        document.getElementById('nominalSetuju').value = '';
    }
}

// ============================================================
// SUBMIT VERIFIKASI (DENGAN VALIDASI)
// ============================================================
document.getElementById('verifikasiForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const status = document.getElementById('verifikasiStatus').value;
    const nominalSetuju = document.getElementById('nominalSetuju').value;
    const catatan = document.getElementById('catatan').value;
    
    if (status === 'disetujui' && (!nominalSetuju || parseInt(nominalSetuju) <= 0)) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Nominal harus diisi!',
            confirmButtonColor: '#4f46e5'
        });
        return;
    }
    
    if (status === 'ditolak' && !catatan.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Catatan penolakan harus diisi!',
            confirmButtonColor: '#4f46e5'
        });
        return;
    }
    
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('status', status);
    
    if (status === 'disetujui' && nominalSetuju && parseInt(nominalSetuju) !== currentMaxDenda) {
        formData.append('nominal_setuju', nominalSetuju);
    }
    if (status === 'ditolak') {
        formData.append('catatan', catatan);
    }
    
    const submitBtn = document.getElementById('submitVerifikasiBtn');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
    
    try {
        const response = await fetch(`/kepala-pustaka/verifikasi/${currentDendaId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonColor: '#4f46e5',
                timer: 1500
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message,
                confirmButtonColor: '#4f46e5'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message,
            confirmButtonColor: '#4f46e5'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
    }
});

// ============================================================
// VERIFIKASI MASSAL
// ============================================================
async function verifikasiMassal(status) {
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih minimal satu denda',
            confirmButtonColor: '#4f46e5'
        });
        return;
    }
    
    const result = await Swal.fire({
        title: status === 'disetujui' ? 'Setujui Denda Massal?' : 'Tolak Denda Massal?',
        html: `Anda akan <strong>${status === 'disetujui' ? 'menyetujui' : 'menolak'}</strong> <strong class="text-indigo-600">${selectedIds.length}</strong> denda.<br>Tindakan ini tidak dapat dibatalkan!`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status === 'disetujui' ? '#22c55e' : '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: status === 'disetujui' ? '✅ Ya, Setujui' : '❌ Ya, Tolak',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    try {
        const response = await fetch('{{ route("kepala-pustaka.verifikasi.massal") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds, status: status })
        });
        
        const resultData = await response.json();
        
        if (resultData.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: resultData.message,
                confirmButtonColor: '#4f46e5',
                timer: 1500
            }).then(() => location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: resultData.message,
                confirmButtonColor: '#4f46e5'
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan: ' + error.message,
            confirmButtonColor: '#4f46e5'
        });
    }
}

// ESC CLOSE (HANYA SATU)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeVerifikasiModal();
});
</script>
@endpush

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection