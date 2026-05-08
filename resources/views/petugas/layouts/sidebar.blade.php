<aside class="w-64 flex-shrink-0 bg-hitam text-white flex flex-col min-h-screen shadow-2xl">

    {{-- Logo Area --}}
    <div class="p-6 text-center border-b border-gray-700 bg-gray-800/50 backdrop-blur-sm sticky top-0 z-10">
        <div class="relative mx-auto w-20 h-20 mb-3 group">
            <div class="absolute inset-0 bg-oren rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity"></div>
            <div class="relative w-full h-full bg-gray-800 rounded-2xl flex items-center justify-center shadow-xl border-2 border-gray-600 group-hover:border-oren transition-all">
                <img src="{{ asset('img/logo.jpg') }}"
                     class="w-14 h-14 object-contain rounded-xl">
            </div>
        </div>
        <h2 class="text-lg font-bold tracking-wide text-white mb-0.5">PERPUSTAKAAN</h2>
        <p class="text-xs font-medium text-gray-400 tracking-wider">SMAN 1 TAMBANG</p>
        
        {{-- User Greeting --}}
        <div class="mt-3 pt-3 border-t border-gray-700">
            <div class="flex items-center justify-center gap-2">
                <div class="w-7 h-7 rounded-full bg-oren flex items-center justify-center shadow-lg">
                    <span class="text-xs font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="text-left">
                    <p class="text-xs font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-hijau rounded-full"></span>
                        <span class="capitalize">{{ auth()->user()->role }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Navigation --}}
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto sidebar-scroll-elegant">
        
        {{-- Dashboard --}}
        <a href="{{ route('petugas.dashboard') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.dashboard') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Dashboard</span>
            @if(request()->routeIs('petugas.dashboard'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Entri Buku --}}
        <a href="{{ route('petugas.buku.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.buku.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.buku.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Entri Buku</span>
            @if(request()->routeIs('petugas.buku.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Katalog --}}
        <a href="{{ route('petugas.katalog.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.katalog.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.katalog.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Katalog</span>
            @if(request()->routeIs('petugas.katalog.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Baca di Tempat --}}
        <a href="{{ route('petugas.baca-ditempat.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.baca-ditempat.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.baca-ditempat.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Baca di Tempat</span>
            @php
                $bacaAktif = \App\Models\BacaDiTempat::where('status', 'sedang_baca')->count();
            @endphp
            @if($bacaAktif > 0)
                <span class="px-1.5 py-0.5 bg-hijau text-white text-[10px] font-bold rounded-full animate-pulse">{{ $bacaAktif }}</span>
            @endif
            @if(request()->routeIs('petugas.baca-ditempat.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Keanggotaan --}}
        <a href="{{ route('petugas.keanggotaan.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.keanggotaan.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.keanggotaan.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Keanggotaan</span>
            @if(request()->routeIs('petugas.keanggotaan.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Kunjungan --}}
        <a href="{{ route('petugas.kunjungan.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.kunjungan.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.kunjungan.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Kunjungan</span>
            @php
                $kunjunganHariIni = \App\Models\Kunjungan::whereDate('tanggal', today())->count();
            @endphp
            @if($kunjunganHariIni > 0)
                <span class="px-1.5 py-0.5 bg-oren text-white text-[10px] font-bold rounded-full animate-pulse">{{ $kunjunganHariIni }}</span>
            @endif
            @if(request()->routeIs('petugas.kunjungan.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Sirkulasi - Dropdown dengan Klik --}}
        <div x-data="{ open: false }" class="space-y-1">
            <button @click="open = !open" 
                    class="w-full group flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
                        {{ request()->routeIs('petugas.sirkulasi.*') ? 'bg-gray-700 text-white' : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.sirkulasi.*') ? 'text-oren' : 'text-gray-400 group-hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Sirkulasi</span>
                </div>
                <svg class="w-3 h-3 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                class="ml-9 space-y-1">
                
                <a href="{{ route('petugas.sirkulasi.peminjaman.index') }}" 
                class="block px-3 py-2 rounded-lg transition-all duration-200 text-xs
                        {{ request()->routeIs('petugas.sirkulasi.peminjaman.*') 
                            ? 'bg-oren text-white font-semibold' 
                            : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-1 rounded-full {{ request()->routeIs('petugas.sirkulasi.peminjaman.*') ? 'bg-white' : 'bg-gray-500' }}"></span>
                        📋 Peminjaman
                    </div>
                </a>

                <a href="{{ route('petugas.sirkulasi.pengembalian.index') }}" 
                class="block px-3 py-2 rounded-lg transition-all duration-200 text-xs
                        {{ request()->routeIs('petugas.sirkulasi.pengembalian.*') 
                            ? 'bg-oren text-white font-semibold' 
                            : 'text-gray-400 hover:text-white hover:bg-gray-700' }}">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-1 rounded-full {{ request()->routeIs('petugas.sirkulasi.pengembalian.*') ? 'bg-white' : 'bg-gray-500' }}"></span>
                        ↩️ Pengembalian
                    </div>
                </a>
            </div>
        </div>

        <li>
            <a href="{{ route('petugas.booking.index') }}" 
            class="flex items-center p-2 text-gray-300 rounded-lg hover:bg-gray-700 group {{ request()->routeIs('petugas.booking.*') ? 'bg-gray-700 text-oren' : '' }}">
                <svg class="w-5 h-5 text-gray-400 transition duration-75 group-hover:text-oren {{ request()->routeIs('petugas.booking.*') ? 'text-oren' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="ml-3">Manajemen Booking</span>
                @php
                    $pendingCount = \App\Models\Booking::where('status', 'menunggu')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-oren text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>

        {{-- OPAC (Public) --}}
        <a href="{{ route('opac.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 text-gray-300 hover:bg-gray-700 hover:scale-105"
           target="_blank">
            <div class="w-7 h-7 flex items-center justify-center text-gray-400 group-hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">OPAC</span>
            <span class="text-[10px] bg-gray-700 px-1.5 py-0.5 rounded-full text-gray-400">Public</span>
        </a>

        {{-- Koleksi Digital --}}
        <a href="{{ route('petugas.koleksi-digital.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.koleksi-digital.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.koleksi-digital.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Koleksi Digital</span>
            @if(request()->routeIs('petugas.koleksi-digital.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Report --}}
        <a href="{{ route('petugas.report.index') }}" 
           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300
           {{ request()->routeIs('petugas.report.*') 
                ? 'bg-oren text-white shadow-lg scale-105' 
                : 'text-gray-300 hover:bg-gray-700 hover:scale-105' }}">
            <div class="w-7 h-7 flex items-center justify-center {{ request()->routeIs('petugas.report.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1">Report</span>
            @if(request()->routeIs('petugas.report.*'))
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            @endif
        </a>

        {{-- Separator --}}
        <div class="my-3 border-t border-gray-700"></div>

        {{-- Logout dengan Konfirmasi --}}
        <button type="button" 
                onclick="confirmLogout()"
                class="w-full group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 text-gray-400 hover:bg-red-500/20 hover:text-white hover:scale-105">
            <div class="w-7 h-7 flex items-center justify-center text-gray-400 group-hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <span class="text-sm font-medium flex-1 text-left">Keluar</span>
        </button>

        {{-- Form Logout Tersembunyi --}}
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
            @csrf
        </form>
    </nav>

    {{-- Footer Credit --}}
    <div class="p-3 text-center text-[10px] text-gray-500 border-t border-gray-700 bg-gray-800/30">
        <p>© {{ date('Y') }} Perpustakaan</p>
        <p class="text-gray-600">SMAN 1 TAMBANG</p>
    </div>
</aside>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Fungsi konfirmasi logout dengan SweetAlert
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: 'Anda akan keluar dari sistem Perpustakaan SMAN 1 Tambang',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mohon tunggu...',
                    text: 'Sedang memproses logout',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        document.getElementById('logout-form').submit();
                    }
                });
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Elegant scrollbar - tipis, muncul saat di-hover */
    .sidebar-scroll-elegant::-webkit-scrollbar {
        width: 3px;
    }
    
    .sidebar-scroll-elegant::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .sidebar-scroll-elegant::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        transition: all 0.3s;
    }
    
    .sidebar-scroll-elegant::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }
    
    /* Untuk Firefox */
    .sidebar-scroll-elegant {
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
    }
</style>
@endpush