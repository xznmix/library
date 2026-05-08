{{-- Tailwind CSS CDN --}}
<script src="https://cdn.tailwindcss.com"></script>

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- Google Font --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Custom Theme Styling --}}
<style>
    * {
        font-family: 'Inter', sans-serif;
    }

    /* Warna Solid */
    :root {
        --oren: #F97316;
        --oren-dark: #EA580C;
        --biru: #3B82F6;
        --biru-dark: #2563EB;
        --hijau: #10B981;
        --hijau-dark: #059669;
        --hitam: #1F2937;
        --hitam-dark: #111827;
        --hitam-light: #F3F4F6;
        --putih: #FFFFFF;
    }

    /* Utility Classes Warna Solid */
    .bg-oren { background-color: #F97316; }
    .bg-oren-dark { background-color: #EA580C; }
    .bg-biru { background-color: #3B82F6; }
    .bg-biru-dark { background-color: #2563EB; }
    .bg-hijau { background-color: #10B981; }
    .bg-hijau-dark { background-color: #059669; }
    .bg-hitam { background-color: #1F2937; }
    .bg-putih { background-color: #FFFFFF; }
    
    .text-oren { color: #F97316; }
    .text-biru { color: #3B82F6; }
    .text-hijau { color: #10B981; }
    .text-hitam { color: #1F2937; }
    .text-putih { color: #FFFFFF; }
    
    .border-oren { border-color: #F97316; }
    .border-biru { border-color: #3B82F6; }
    .border-hijau { border-color: #10B981; }
    
    .hover\:bg-oren-dark:hover { background-color: #EA580C; }
    .hover\:bg-biru-dark:hover { background-color: #2563EB; }
    .hover\:bg-hijau-dark:hover { background-color: #059669; }
    .hover\:text-oren:hover { color: #F97316; }
    .hover\:text-biru:hover { color: #3B82F6; }

    /* DEFAULT TERANG */
    body {
        background-color: #F3F4F6;
    }
    
    /* DARK MODE (hanya aktif jika ada class dark) */
    .dark body {
        background-color: #111827;
    }

    /* Card style */
    .card {
        border-radius: 16px;
        background: white;
        padding: 20px;
        transition: all 0.2s ease-in-out;
        border: 1px solid #e5e7eb;
    }
    
    .dark .card {
        background: #1f2937;
        border-color: #374151;
    }

    /* Sidebar Styling */
    aside {
        box-shadow: 4px 0 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    
    @media (max-width: 1024px) {
        aside {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 40;
            transform: translateX(0);
        }
        aside.-translate-x-full {
            transform: translateX(-100%);
        }
    }

    nav a {
        border-radius: 10px;
        margin: 6px 12px;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .dark ::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .dark ::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Animations */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .animate-pulse-slow {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>