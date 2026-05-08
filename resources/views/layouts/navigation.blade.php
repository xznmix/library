<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="font-semibold text-gray-800">
                    Dashboard
                </a>
            </div>

            <!-- Right -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">

                <!-- User Info -->
                <div class="text-sm text-gray-700">
                    {{ Auth::user()->name }}
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Hamburger -->
            <div class="flex items-center sm:hidden">
                <button @click="open = !open"
                    class="p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Menu -->
    <div x-show="open" class="sm:hidden border-t border-gray-200">

        <div class="px-4 py-3">
            <div class="text-sm font-medium text-gray-800">
                {{ Auth::user()->name }}
            </div>
            <div class="text-sm text-gray-500">
                {{ Auth::user()->email }}
            </div>
        </div>

        <div class="px-4 pb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="block w-full text-left text-red-600 hover:text-red-800">
                    Logout
                </button>
            </form>
        </div>

    </div>
</nav>
