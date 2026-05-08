<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-100 dark:bg-indigo-900/30 mb-6">
                    <svg class="w-12 h-12 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Halaman Tidak Ditemukan</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Maaf, halaman yang Anda cari tidak dapat ditemukan.
                </p>
                <a href="{{ route('kepala-pustaka.dashboard') }}" 
                   class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>