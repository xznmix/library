@extends('kepala-pustaka.layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Log Aktivitas
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Rekam jejak semua aktivitas dalam sistem
            </p>
        </div>
        
        {{-- Export Buttons --}}
        <div class="flex gap-2 mt-4 md:mt-0">
            <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-5 border border-indigo-100 dark:border-indigo-800">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-xs text-indigo-600 dark:text-indigo-400">Total Aktivitas</p>
                <p class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">{{ $totalAktivitas ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-indigo-600 dark:text-indigo-400">Hari Ini</p>
                <p class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">{{ $statistik['hari_ini'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-indigo-600 dark:text-indigo-400">Minggu Ini</p>
                <p class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">{{ $statistik['minggu_ini'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-indigo-600 dark:text-indigo-400">Bulan Ini</p>
                <p class="text-2xl font-bold text-indigo-800 dark:text-indigo-200">{{ $statistik['bulan_ini'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="kepala_pustaka" {{ request('role') == 'kepala_pustaka' ? 'selected' : '' }}>Kepala Pustaka</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Aksi</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="">Semua Aksi</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="verifikasi" {{ request('action') == 'verifikasi' ? 'selected' : '' }}>Verifikasi</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">User</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
                    <option value="">Semua User</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" 
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700">
            </div>
            
            <div class="md:col-span-5 flex justify-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                    Filter
                </button>
                <a href="{{ route('kepala-pustaka.laporan.aktivitas') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Top Users --}}
    @if(isset($userAktif) && count($userAktif) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5">
        <h3 class="font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            User Paling Aktif
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach($userAktif as $index => $item)
            <div class="text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg mb-2">
                    {{ $index + 1 }}
                </div>
                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $item->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $item->total }} aktivitas</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Timeline Aktivitas --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 dark:text-white">📋 Daftar Aktivitas</h3>
            <span class="text-xs bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                {{ $aktivitas->total() ?? 0 }} aktivitas
            </span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($aktivitas as $log)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start gap-4">
                    {{-- Icon berdasarkan aksi --}}
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                        @if($log->action == 'create') bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                        @elseif($log->action == 'update') bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400
                        @elseif($log->action == 'delete') bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400
                        @elseif($log->action == 'verifikasi') bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400
                        @elseif($log->action == 'login') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400
                        @elseif($log->action == 'logout') bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400
                        @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 @endif">
                        @if($log->action == 'create')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        @elseif($log->action == 'update')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        @elseif($log->action == 'delete')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        @elseif($log->action == 'verifikasi')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($log->action == 'login')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        @elseif($log->action == 'logout')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    
                    {{-- Konten --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-gray-800 dark:text-white">{{ $log->user->name ?? 'Sistem' }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($log->role == 'admin') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($log->role == 'petugas') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($log->role == 'kepala_pustaka') bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                {{ ucfirst(str_replace('_', ' ', $log->role)) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $log->description }}</p>
                        
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                                </svg>
                                IP: {{ $log->ip_address }}
                            </span>
                            @if($log->model)
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                {{ $log->model }} #{{ $log->model_id }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Action badge --}}
                    <span class="px-2 py-1 text-xs rounded-full whitespace-nowrap
                        @if($log->action == 'create') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                        @elseif($log->action == 'update') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($log->action == 'delete') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                        @elseif($log->action == 'verifikasi') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst($log->action) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada aktivitas</p>
                <p class="text-sm text-gray-400 mt-1">Coba filter dengan periode berbeda</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($aktivitas instanceof \Illuminate\Pagination\LengthAwarePaginator && $aktivitas->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $aktivitas->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("kepala-pustaka.laporan.aktivitas.export") }}?' + params.toString();
}
</script>
@endpush
@endsection