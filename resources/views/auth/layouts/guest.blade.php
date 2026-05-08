<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Perpustakaan SMAN 1 Tambang')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        oren: {
                            500: '#F97316',
                            600: '#EA580C',
                        },
                        biru: {
                            500: '#3B82F6',
                            600: '#2563EB',
                        },
                        hijau: {
                            500: '#10B981',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #3B82F6 0%, #1F2937 100%);
            min-height: 100vh;
        }
        
        .text-oren { color: #F97316; }
        .text-biru { color: #3B82F6; }
        .text-hijau { color: #10B981; }
        .bg-oren { background-color: #F97316; }
        .bg-biru { background-color: #3B82F6; }
        .bg-biru-dark { background-color: #2563EB; }
        .hover\:bg-biru-dark:hover { background-color: #2563EB; }
        .hover\:bg-oren:hover { background-color: #F97316; }
        .border-oren { border-color: #F97316; }
        
        input:focus {
            outline: none;
        }
    </style>
    
    @stack('styles')
</head>
<body class="flex items-center justify-center p-4">
    
    <!-- Memperlebar container dari max-w-md menjadi max-w-2xl -->
    <div class="w-full max-w-2xl">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-md mb-4">
                <img src="{{ asset('storage/logo.jpg') }}" alt="Logo" class="w-14 h-14 rounded-full object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Crect fill=\'%233B82F6\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50\' y=\'67\' font-size=\'45\' text-anchor=\'middle\' fill=\'%23F97316\'%3E📚%3C/text%3E%3C/svg%3E'">
            </div>
            <h2 class="text-2xl font-bold text-white">Perpustakaan Digital</h2>
            <p class="text-blue-100 text-base mt-2">SMAN 1 TAMBANG</p>
        </div>
        
        <!-- Card Form - Memperlebar padding dari px-8 md:px-10 menjadi px-10 md:px-14 -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-10 md:px-14 py-12 bg-white">
                @yield('content')
            </div>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-white/60 text-sm">
                &copy; {{ date('Y') }} Perpustakaan SMAN 1 Tambang
            </p>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>