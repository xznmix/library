{{-- jQuery (optional) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Alpine.js untuk interaktivitas --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    // NONAKTIFKAN DARK MODE - SELALU MODE TERANG
    // Hapus dark mode dari localStorage dan HTML
    localStorage.removeItem('dark-mode');
    document.documentElement.classList.remove('dark');
    
    // Hapus semua class dark: dari elemen
    function removeDarkClasses() {
        document.querySelectorAll('[class*="dark:"]').forEach(el => {
            const classes = el.className.split(' ');
            const newClasses = classes.filter(c => !c.startsWith('dark:'));
            el.className = newClasses.join(' ');
        });
    }
    
    removeDarkClasses();
    
    // Observer untuk menghapus dark class yang ditambahkan kemudian
    const observer = new MutationObserver(removeDarkClasses);
    observer.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['class'] });

    // Inisialisasi
    document.addEventListener('DOMContentLoaded', function() {
        // User menu toggle
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenu = document.getElementById('userMenu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function() {
                userMenu.classList.add('hidden');
            });
        }

        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('aside');
        
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('absolute');
                sidebar.classList.toggle('z-50');
            });
        }

        // Card hover effects
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            card.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });
    });

    // Real-time clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit',
            second: '2-digit'
        });
        const clockElement = document.getElementById('liveClock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>