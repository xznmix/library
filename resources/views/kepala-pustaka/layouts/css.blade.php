{{-- Tailwind CSS CDN dengan Custom Color Palette --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    // Warna standar sesuai permintaan
                    oren: {
                        50: '#FFF7ED',
                        100: '#FFEDD5',
                        200: '#FED7AA',
                        300: '#FDBA74',
                        400: '#FB923C',
                        500: '#F97316',
                        600: '#EA580C',
                        700: '#C2410C',
                        800: '#9A3412',
                        900: '#7C2D12',
                    },
                    biru: {
                        50: '#EFF6FF',
                        100: '#DBEAFE',
                        200: '#BFDBFE',
                        300: '#93C5FD',
                        400: '#60A5FA',
                        500: '#3B82F6',
                        600: '#2563EB',
                        700: '#1D4ED8',
                        800: '#1E40AF',
                        900: '#1E3A8A',
                    },
                    hijau: {
                        50: '#ECFDF5',
                        100: '#D1FAE5',
                        200: '#A7F3D0',
                        300: '#6EE7B7',
                        400: '#34D399',
                        500: '#10B981',
                        600: '#059669',
                        700: '#047857',
                        800: '#065F46',
                        900: '#064E3B',
                    },
                    hitam: {
                        50: '#F9FAFB',
                        100: '#F3F4F6',
                        200: '#E5E7EB',
                        300: '#D1D5DB',
                        400: '#9CA3AF',
                        500: '#6B7280',
                        600: '#4B5563',
                        700: '#374151',
                        800: '#1F2937',
                        900: '#111827',
                    },
                    putih: '#FFFFFF',
                }
            }
        }
    }
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Inter', sans-serif; }

    body {
        background-color: #F3F4F6; /* hitam-100 */
    }

    /* Sidebar menggunakan warna hitam-800 */
    .sidebar-custom {
        background-color: #1F2937; /* hitam-800 */
    }

    /* Menu Aktif menggunakan warna oren-500 */
    .nav-active {
        background: linear-gradient(90deg, rgba(249,115,22,0.2) 0%, rgba(249,115,22,0) 100%);
        border-left: 4px solid #F97316;
        color: #F97316 !important;
    }

    /* Card Styling */
    .dashboard-card {
        border-radius: 12px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    /* Border aksen kartu */
    .card-accent-hijau::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: #10B981; }
    .card-accent-biru::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: #3B82F6; }
    .card-accent-oren::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: #F97316; }
    
    /* Warna tambahan untuk komponen */
    .bg-brand-green { background-color: #10B981; }
    .bg-brand-blue { background-color: #3B82F6; }
    .bg-brand-orange { background-color: #F97316; }
    .bg-brand-black { background-color: #1F2937; }
    .bg-brand-light { background-color: #F3F4F6; }
    
    .text-brand-green { color: #10B981; }
    .text-brand-blue { color: #3B82F6; }
    .text-brand-orange { color: #F97316; }
    .text-brand-black { color: #1F2937; }
    
    .border-brand-green { border-color: #10B981; }
    .border-brand-blue { border-color: #3B82F6; }
    .border-brand-orange { border-color: #F97316; }
</style>