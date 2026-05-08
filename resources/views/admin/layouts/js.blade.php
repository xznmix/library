{{-- jQuery (optional) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Alpine.js untuk interaktivitas --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    // Dark Mode Toggle
    function toggleDarkMode() {
        if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            localStorage.setItem('dark-mode', 'false');
            document.documentElement.classList.remove('dark');
        } else {
            localStorage.setItem('dark-mode', 'true');
            document.documentElement.classList.add('dark');
        }
    }

    // Inisialisasi Dark Mode
    document.addEventListener('DOMContentLoaded', function() {
        // Dark mode
        if (localStorage.getItem('dark-mode') === 'true' || (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // User menu toggle
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenu = document.getElementById('userMenu');
        
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenu.classList.toggle('hidden');
            });

            // Close menu when clicking outside
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

        // Notification animation
        const notifications = document.querySelectorAll('.fa-bell');
        notifications.forEach(bell => {
            setInterval(() => {
                bell.classList.toggle('animate-pulse');
            }, 3000);
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