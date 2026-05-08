{{-- Tailwind CSS CDN --}}
<script src="https://cdn.tailwindcss.com"></script>

{{-- Font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- Google Font --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

{{-- Custom Theme Styling --}}
<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #F3F4F6;
    }

    /* Warna Solid */
    .bg-biru { background-color: #3B82F6; }
    .bg-biru-dark { background-color: #2563EB; }
    .bg-oren { background-color: #F97316; }
    .bg-oren-dark { background-color: #EA580C; }
    .bg-hijau { background-color: #10B981; }
    .bg-hitam { background-color: #1F2937; }
    
    .text-biru { color: #3B82F6; }
    .text-oren { color: #F97316; }
    .text-hijau { color: #10B981; }
    .text-hitam { color: #1F2937; }
    
    .border-biru { border-color: #3B82F6; }
    .border-oren { border-color: #F97316; }
    .border-hijau { border-color: #10B981; }
    
    .hover\:bg-biru-dark:hover { background-color: #2563EB; }
    .hover\:bg-oren-dark:hover { background-color: #EA580C; }
    .hover\:bg-hijau-dark:hover { background-color: #059669; }
    .hover\:text-biru:hover { color: #3B82F6; }
    .hover\:text-oren:hover { color: #F97316; }

    /* Dashboard Card Style */
    .card {
        border-radius: 16px;
        background: white;
        padding: 20px;
        transition: 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    /* Border Accent for Dashboard Stats */
    .border-left-primary {
        border-left: 6px solid #3B82F6;
    }

    .border-left-success {
        border-left: 6px solid #10B981;
    }

    .border-left-info {
        border-left: 6px solid #0891b2;
    }

    .border-left-warning {
        border-left: 6px solid #F97316;
    }

    /* Shadow Soft */
    .shadow {
        box-shadow: 0 4px 15px rgba(0,0,0,0.05) !important;
    }

    /* Sidebar Styling Fix */
    aside {
        box-shadow: 4px 0 15px rgba(0,0,0,0.08);
    }

    nav a {
        border-radius: 10px;
        margin: 6px 12px;
    }

    nav a:hover {
        transition: 0.2s;
    }

    /* Topbar Fix */
    header {
        border-bottom: 1px solid #e5e7eb;
    }

    /* Footer */
    footer {
        border-top: 1px solid #e5e7eb;
        background: white;
    }
</style>