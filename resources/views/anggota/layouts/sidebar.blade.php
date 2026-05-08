<aside class="w-64 bg-hitam text-white flex flex-col justify-between shadow-xl z-10">

    <div>
        <div class="p-6 text-center border-b border-hitam-700">
            <div class="relative mx-auto w-20 h-20 mb-3">
                <div class="w-full h-full rounded-full bg-oren flex items-center justify-center text-2xl font-bold text-white overflow-hidden">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @elseif(auth()->user()->foto_ktp)
                        <img src="{{ asset('storage/' . auth()->user()->foto_ktp) }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-hijau rounded-full border-2 border-hitam"></div>
            </div>
            <h1 class="font-bold text-lg mb-1">{{ auth()->user()->name }}</h1>
            <p class="text-xs text-gray-400 flex items-center justify-center gap-1">
                <span class="w-2 h-2 bg-hijau rounded-full"></span>
                {{ ucfirst(auth()->user()->role ?? 'Anggota') }}
                @if(auth()->user()->kelas)
                    • Kelas {{ auth()->user()->kelas }}
                @endif
            </p>
            @if(auth()->user()->no_anggota)
            <div class="mt-3 text-xs bg-gray-700 py-1 px-3 rounded-full inline-block">
                <i class="fas fa-id-card mr-1"></i>{{ auth()->user()->no_anggota }}
            </div>
            @endif
        </div>

        <nav class="mt-8 px-3">
            <a href="{{ route('anggota.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group
               {{ request()->routeIs('anggota.dashboard') ? 'bg-oren text-white font-semibold shadow-lg' : 'hover:bg-gray-700' }}">
                <div class="w-8 h-8 flex items-center justify-center {{ request()->routeIs('anggota.dashboard') ? 'bg-white/20' : 'bg-gray-600' }} rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-home text-sm"></i>
                </div>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('opac.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group hover:bg-gray-700"
               target="_blank">
                <div class="w-8 h-8 flex items-center justify-center bg-gray-600 rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <span>OPAC</span>
                <span class="ml-auto text-[10px] bg-gray-600 px-2 py-0.5 rounded-full">Public</span>
            </a>

            <a href="{{ route('anggota.koleksi-digital.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group
               {{ request()->routeIs('anggota.koleksi-digital.*') ? 'bg-oren text-white font-semibold shadow-lg' : 'hover:bg-gray-700' }}">
                <div class="w-8 h-8 flex items-center justify-center {{ request()->routeIs('anggota.koleksi-digital.*') ? 'bg-white/20' : 'bg-gray-600' }} rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-book-open text-sm"></i>
                </div>
                <span>Koleksi Digital</span>
            </a>

            <a href="{{ route('anggota.riwayat.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group
               {{ request()->routeIs('anggota.riwayat.*') ? 'bg-oren text-white font-semibold shadow-lg' : 'hover:bg-gray-700' }}">
                <div class="w-8 h-8 flex items-center justify-center {{ request()->routeIs('anggota.riwayat.*') ? 'bg-white/20' : 'bg-gray-600' }} rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-history text-sm"></i>
                </div>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('anggota.booking.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group
               {{ request()->routeIs('anggota.booking.*') ? 'bg-oren text-white font-semibold shadow-lg' : 'hover:bg-gray-700' }}">
                <div class="w-8 h-8 flex items-center justify-center {{ request()->routeIs('anggota.booking.*') ? 'bg-white/20' : 'bg-gray-600' }} rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
                <span>Booking Saya</span>
            </a>

            <a href="{{ route('anggota.profil.index') }}" 
               class="flex items-center px-4 py-3 rounded-xl mb-2 transition-all group
               {{ request()->routeIs('anggota.profil.*') ? 'bg-oren text-white font-semibold shadow-lg' : 'hover:bg-gray-700' }}">
                <div class="w-8 h-8 flex items-center justify-center {{ request()->routeIs('anggota.profil.*') ? 'bg-white/20' : 'bg-gray-600' }} rounded-lg mr-3 group-hover:bg-gray-500 transition-colors">
                    <i class="fas fa-user-circle text-sm"></i>
                </div>
                <span>Profil Saya</span>
            </a>
        </nav>
    </div>

    <div class="p-6 border-t border-gray-700">
        <div class="flex items-center mb-4 px-3 py-2 bg-gray-700 rounded-lg">
            <div class="w-10 h-10 rounded-full bg-oren flex items-center justify-center text-white font-bold overflow-hidden mr-3">
                @if(auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-full h-full object-cover">
                @elseif(auth()->user()->foto_ktp)
                    <img src="{{ asset('storage/' . auth()->user()->foto_ktp) }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role ?? 'Anggota' }}</p>
            </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" id="sidebarLogoutForm">
            @csrf
            <button type="button" onclick="confirmLogout()" class="w-full bg-gray-700 hover:bg-gray-600 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>

</aside>