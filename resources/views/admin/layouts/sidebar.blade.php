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
        </div>

        <nav class="mt-6">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-home w-6"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="{{ route('admin.kelola-akun.index') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('admin.kelola-akun.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-users-cog w-6"></i>
                <span class="font-medium">Kelola Akun</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}"
                class="flex items-center px-6 py-3 transition-all duration-200
                {{ request()->routeIs('admin.laporan.*') ? 'nav-active' : 'text-hitam-400 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-chart-bar w-6"></i>
                <span class="font-medium">Laporan</span>
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
            confirmButtonColor: '#F97316', // Menggunakan Oren
            cancelButtonColor: '#1F2937', // Hitam-800
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