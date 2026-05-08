<aside class="w-64 sidebar-custom text-white flex flex-col justify-between shadow-xl z-10">
    <div>
        <div class="p-6 text-center border-b border-hitam-700">
            <div class="relative mx-auto w-20 h-20 mb-3">
                <img src="{{ asset('img/logo.jpg') }}" 
                    class="w-full h-full rounded-full object-cover border-4 border-oren-500 shadow-lg" 
                    alt="Logo">
            </div>
            <h1 class="font-bold text-lg text-oren-500">PERPUSTAKAAN</h1>
            <p class="text-[10px] text-hitam-400 uppercase tracking-widest">SMAN 1 TAMBANG</p>
            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-hitam-700/50 rounded-full">
                <span class="w-2 h-2 bg-oren-400 rounded-full animate-pulse"></span>
                <span class="text-xs font-medium text-hitam-300">Kepala Pustaka</span>
            </div>
        </div>

        {{-- User Info Card --}}
        <div class="mx-4 mt-4 p-3 bg-hitam-700/30 rounded-xl border border-hitam-700 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-oren-400 to-oren-500 flex items-center justify-center shadow-lg flex-shrink-0">
                <span class="text-sm font-bold text-hitam-900">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                <div class="flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 bg-hijau-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-hitam-300">Online</span>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="mx-4 mt-4 grid grid-cols-2 gap-2">
            <div class="bg-hitam-700/30 rounded-lg p-2 text-center border border-hitam-700">
                <p class="text-xs text-hitam-400">Denda Pending</p>
                <p class="text-lg font-bold text-oren-400">{{ $dendaPending ?? 0 }}</p>
            </div>
            <div class="bg-hitam-700/30 rounded-lg p-2 text-center border border-hitam-700">
                <p class="text-xs text-hitam-400">Kunjungan</p>
                <p class="text-lg font-bold text-hijau-400">{{ $kunjunganHariIni ?? 0 }}</p>
            </div>
        </div>

        {{-- Main Navigation --}}
        <nav class="mt-6">
            <a href="{{ route('kepala-pustaka.dashboard') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('kepala-pustaka.dashboard') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-home w-6"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('kepala-pustaka.verifikasi.index') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('kepala-pustaka.verifikasi.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-check-circle w-6"></i>
                <span class="font-medium">Verifikasi Denda</span>
                @if(($dendaPending ?? 0) > 0)
                    <span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full animate-pulse">{{ $dendaPending }}</span>
                @endif
            </a>

            <a href="{{ route('kepala-pustaka.audit.buku') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('kepala-pustaka.audit.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-clipboard-list w-6"></i>
                <span class="font-medium">Audit Buku</span>
            </a>

            {{-- Laporan Dropdown --}}
            <div x-data="{ open: @json(request()->routeIs('kepala-pustaka.laporan.*')) }" class="relative">
                <button @click="open = !open" 
                    class="w-full flex items-center justify-between px-6 py-3 transition-all duration-200
                    {{ request()->routeIs('kepala-pustaka.laporan.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar w-6"></i>
                        <span class="font-medium ml-0">Laporan</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="ml-6 space-y-1 mt-1">
                    
                    <a href="{{ route('kepala-pustaka.laporan.denda') }}" 
                        class="flex items-center px-6 py-2 text-sm rounded-lg transition-all duration-200
                        {{ request()->routeIs('kepala-pustaka.laporan.denda') 
                            ? 'text-oren-500 bg-white/5' 
                            : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span>Laporan Denda</span>
                    </a>

                    <a href="{{ route('kepala-pustaka.laporan.peminjaman') }}" 
                        class="flex items-center px-6 py-2 text-sm rounded-lg transition-all duration-200
                        {{ request()->routeIs('kepala-pustaka.laporan.peminjaman') 
                            ? 'text-oren-500 bg-white/5' 
                            : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-book w-5"></i>
                        <span>Laporan Peminjaman</span>
                    </a>

                    <a href="{{ route('kepala-pustaka.laporan.aktivitas') }}" 
                        class="flex items-center px-6 py-2 text-sm rounded-lg transition-all duration-200
                        {{ request()->routeIs('kepala-pustaka.laporan.aktivitas') 
                            ? 'text-oren-500 bg-white/5' 
                            : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                        <i class="fas fa-history w-5"></i>
                        <span>Log Aktivitas</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

    <div class="p-4 border-t border-hitam-700">
        <button type="button" onclick="confirmLogout()"
            class="w-full flex items-center justify-center px-4 py-2 bg-oren-500/10 hover:bg-oren-500 text-oren-500 hover:text-white rounded-lg transition-all text-xs font-bold uppercase">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </button>
    </div>
</aside>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#F97316',
            cancelButtonColor: '#1F2937',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>