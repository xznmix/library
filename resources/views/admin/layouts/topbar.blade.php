<header class="bg-white border-b border-gray-200 px-6 py-3 flex justify-between items-center">
    <div class="flex items-center">
        <button id="sidebarToggle" class="md:hidden mr-4 text-hitam-800">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-sm font-bold text-biru-600 uppercase tracking-tight">Halaman Administrasi</h2>
    </div>

    <div class="flex items-center space-x-4">
        {{-- Status Indikator Hijau --}}
        <div class="hidden sm:flex items-center text-[11px] font-bold text-hijau-600 bg-hijau-50 px-3 py-1 rounded-full border border-hijau-200">
            <i class="fas fa-check-circle mr-1"></i> SISTEM AKTIF
        </div>

        <div class="flex items-center space-x-3 pl-4 border-l border-gray-100">
            <div class="text-right hidden lg:block">
                <p class="text-xs font-bold text-hitam-800">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-hitam-500 uppercase">Administrator</p>
            </div>
            {{-- Tombol Profil Warna Oren --}}
            <button id="userMenuButton" class="w-10 h-10 rounded-full bg-oren-500 text-white font-bold shadow-sm hover:scale-105 transition-transform">
                {{ substr(auth()->user()->name, 0, 1) }}
            </button>
        </div>
    </div>
</header>