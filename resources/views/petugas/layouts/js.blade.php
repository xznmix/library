<script>
// Dark Mode - SINGLE SOURCE OF TRUTH
(function() {
    // Fungsi utama untuk mengatur theme
    function setTheme(isDark) {
        if (isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('dark-mode', 'true');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('dark-mode', 'false');
        }
        
        // Update semua icon toggle yang ada
        updateAllToggleIcons();
    }
    
    // Update semua icon toggle di berbagai tempat
    function updateAllToggleIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        
        // Update topbar toggle jika ada
        const topbarToggle = document.getElementById('topbarDarkModeToggle');
        if (topbarToggle) {
            const sunIcon = topbarToggle.querySelector('.sun-icon');
            const moonIcon = topbarToggle.querySelector('.moon-icon');
            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
            }
        }
        
        // Update settings toggle jika ada
        const settingsToggle = document.getElementById('darkModeToggle');
        if (settingsToggle) {
            const sunIcon = settingsToggle.querySelector('.sun-icon');
            const moonIcon = settingsToggle.querySelector('.moon-icon');
            const modeText = settingsToggle.querySelector('.mode-text');
            if (sunIcon && moonIcon) {
                if (isDark) {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                    if (modeText) modeText.textContent = 'Mode Terang';
                } else {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                    if (modeText) modeText.textContent = 'Mode Gelap';
                }
            }
        }
    }
    
    // Load saved theme - DEFAULT TERANG
    const savedTheme = localStorage.getItem('dark-mode');
    
    if (savedTheme === null) {
        // Belum pernah pilih → DEFAULT TERANG
        setTheme(false);
    } else if (savedTheme === 'true') {
        // Pernah pilih GELAP
        setTheme(true);
    } else {
        // Pernah pilih TERANG
        setTheme(false);
    }
    
    // Event listener untuk tombol toggle setelah DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        const topbarToggle = document.getElementById('topbarDarkModeToggle');
        if (topbarToggle) {
            // Hapus listener lama jika ada
            const newToggle = topbarToggle.cloneNode(true);
            topbarToggle.parentNode.replaceChild(newToggle, topbarToggle);
            
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isDark = !document.documentElement.classList.contains('dark');
                setTheme(isDark);
            });
        }
        
        const settingsToggle = document.getElementById('darkModeToggle');
        if (settingsToggle) {
            const newToggle = settingsToggle.cloneNode(true);
            settingsToggle.parentNode.replaceChild(newToggle, settingsToggle);
            
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isDark = !document.documentElement.classList.contains('dark');
                setTheme(isDark);
            });
        }
        
        updateAllToggleIcons();
    });
})();
</script>