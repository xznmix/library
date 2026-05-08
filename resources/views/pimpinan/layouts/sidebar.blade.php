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
                <span class="text-xs font-medium text-hitam-300">Pimpinan / Kepala Sekolah</span>
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
                <p class="text-xs text-hitam-400">Tahun Ajaran</p>
                <p class="text-sm font-bold text-white">{{ date('Y') }}/{{ date('Y')+1 }}</p>
            </div>
            <div class="bg-hitam-700/30 rounded-lg p-2 text-center border border-hitam-700">
                <p class="text-xs text-hitam-400">Semester</p>
                <p class="text-sm font-bold text-white">{{ date('m') <= 6 ? 'Genap' : 'Ganjil' }}</p>
            </div>
        </div>

        {{-- Main Navigation --}}
        <nav class="mt-6">
            {{-- Dashboard --}}
            <a href="{{ route('pimpinan.dashboard') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.dashboard') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-home w-6"></i>
                <span class="font-medium">Dashboard Eksekutif</span>
            </a>

            {{-- Laporan Peminjaman --}}
            <a href="{{ route('pimpinan.laporan.peminjaman') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.laporan.peminjaman') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-book w-6"></i>
                <span class="font-medium">Monitoring Peminjaman</span>
            </a>

            {{-- Laporan Kunjungan --}}
            <a href="{{ route('pimpinan.laporan.kunjungan') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.laporan.kunjungan') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-users w-6"></i>
                <span class="font-medium">Monitoring Kunjungan</span>
            </a>

            {{-- Laporan Keuangan --}}
            <a href="{{ route('pimpinan.laporan.keuangan') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.laporan.keuangan') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-money-bill-wave w-6"></i>
                <span class="font-medium">Monitoring Keuangan</span>
            </a>

            {{-- Kinerja & KPI --}}
            <a href="{{ route('pimpinan.kinerja.index') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.kinerja.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-chart-line w-6"></i>
                <span class="font-medium">Monitoring Kinerja & KPI</span>
            </a>

            {{-- Separator --}}
            <div class="my-4 border-t border-hitam-700"></div>

            {{-- Export Data --}}
            <a href="{{ route('pimpinan.export.index') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('pimpinan.export.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-download w-6"></i>
                <span class="font-medium">Export Data</span>
            </a>
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