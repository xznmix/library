<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm sticky top-0 z-30 transition-colors duration-200">
    <div class="px-6 py-3 flex items-center justify-between">

        {{-- Left: Toggle Sidebar + Page Title --}}
        <div class="flex items-center gap-4">
            <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <span class="hidden md:inline">📚</span>
                    @yield('title', 'Dashboard Petugas')
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>

        {{-- Right Side --}}
        <div class="flex items-center gap-3">

            {{-- Refresh --}}
            <button onclick="location.reload()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>

            {{-- Separator --}}
            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

            {{-- Profile --}}
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="flex items-center gap-3 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->role }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-biru flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition @click.away="open=false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>