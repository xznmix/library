<header class="bg-white shadow-sm px-4 md:px-6 py-3 flex justify-between items-center transition-all duration-200 sticky top-0 z-40">
    {{-- Left Side --}}
    <div class="flex items-center">
        <button id="sidebarToggle" class="md:hidden mr-3 p-2.5 rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-bars text-gray-700 text-xl"></i>
        </button>
        
        <div class="flex items-center">
            <h2 class="text-lg md:text-xl font-semibold text-hitam">
                @yield('page-title', 'Dashboard')
            </h2>
            
            <span class="ml-3 text-sm text-gray-500 hidden md:inline">
                👋 Halo, <span class="font-medium text-biru">{{ auth()->user()->name }}</span>
            </span>
        </div>
        
        <span class="ml-3 hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-hijau-100 text-hijau-800">
            <span class="w-1.5 h-1.5 bg-hijau rounded-full mr-1.5"></span>
            Live
        </span>
    </div>

    {{-- Right Side --}}
    <div class="flex items-center space-x-3">
        {{-- Notifications Icon --}}
        <div class="relative">
            <button id="notificationButton" class="p-2.5 rounded-lg hover:bg-gray-100 relative transition-colors">
                <i class="fas fa-bell text-gray-600 text-lg"></i>
                <span id="notificationBadge" class="absolute -top-1 -right-1 min-w-[20px] h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-sm px-1 hidden">0</span>
            </button>
            
            {{-- Notification Dropdown --}}
            <div id="notificationDropdown" class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border border-gray-100 hidden z-50">
                <div class="p-3 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                    <button id="markAllReadBtn" class="text-xs text-biru hover:text-biru-dark transition">
                        Tandai semua sudah dibaca
                    </button>
                </div>
                <div id="notificationList" class="max-h-96 overflow-y-auto">
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat...
                    </div>
                </div>
                <div class="p-2 border-t border-gray-100 text-center">
                    <a href="{{ route('anggota.notifications.index') }}" class="text-xs text-biru hover:underline">
                        Lihat semua notifikasi
                    </a>
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" id="profileDropdown">
            <button id="profileButton" class="flex items-center focus:outline-none group">
                <div class="w-10 h-10 rounded-full bg-biru flex items-center justify-center overflow-hidden border-2 border-transparent group-hover:border-oren transition-all shadow-sm text-white font-bold">
                    @if(auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @elseif(auth()->user()->foto_ktp)
                        <img src="{{ asset('storage/' . auth()->user()->foto_ktp) }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
            </button>

            <div id="profileMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50 hidden">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->no_anggota)
                        <p class="text-xs text-biru mt-1 font-mono">{{ auth()->user()->no_anggota }}</p>
                    @endif
                </div>

                <a href="{{ route('anggota.profil.index') }}" 
                   class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-user-circle w-5 mr-3 text-biru"></i>
                    <span>Profil Saya</span>
                </a>

                <div class="border-t border-gray-100 my-1"></div>

                <button type="button" 
                        onclick="confirmLogout()"
                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                    <i class="fas fa-sign-out-alt w-5 mr-3 text-red-500"></i>
                    <span class="text-left">Keluar</span>
                </button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>

<script>
// Load notifications
function loadNotifications() {
    fetch('{{ route("anggota.notifications.latest") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.unread_count > 0) {
                badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
            
            const listContainer = document.getElementById('notificationList');
            if (data.notifications.length === 0) {
                listContainer.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-3xl mb-2 opacity-50"></i>
                        <p class="text-sm">Tidak ada notifikasi</p>
                    </div>
                `;
            } else {
                listContainer.innerHTML = data.notifications.map(notif => `
                    <div class="p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 ${!notif.is_read ? 'bg-biru-50/30' : ''}" data-id="${notif.id}">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                ${getNotificationIcon(notif.type)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(notif.title)}</p>
                                <p class="text-xs text-gray-500 mt-0.5">${escapeHtml(notif.message)}</p>
                                <p class="text-xs text-gray-400 mt-1">${formatTime(notif.created_at)}</p>
                            </div>
                            ${!notif.is_read ? `
                            <button onclick="markAsRead(${notif.id})" class="flex-shrink-0 text-biru hover:text-biru-dark">
                                <i class="fas fa-check-circle text-sm"></i>
                            </button>
                            ` : ''}
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function getNotificationIcon(type) {
    const icons = {
        'info': '<div class="w-8 h-8 rounded-full bg-biru-100 flex items-center justify-center"><i class="fas fa-info-circle text-biru text-sm"></i></div>',
        'warning': '<div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center"><i class="fas fa-exclamation-triangle text-oren text-sm"></i></div>',
        'success': '<div class="w-8 h-8 rounded-full bg-hijau-100 flex items-center justify-center"><i class="fas fa-check-circle text-hijau text-sm"></i></div>',
        'danger': '<div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center"><i class="fas fa-times-circle text-red-500 text-sm"></i></div>'
    };
    return icons[type] || icons['info'];
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Baru saja';
    if (minutes < 60) return `${minutes} menit lalu`;
    if (hours < 24) return `${hours} jam lalu`;
    if (days < 7) return `${days} hari lalu`;
    return date.toLocaleDateString('id-ID');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function markAsRead(id) {
    fetch(`{{ url("anggota/notifications") }}/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(() => {
        loadNotifications();
    })
    .catch(error => console.error('Error:', error));
}

function markAllAsRead() {
    fetch('{{ route("anggota.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(() => {
        loadNotifications();
    })
    .catch(error => console.error('Error:', error));
}

document.getElementById('markAllReadBtn')?.addEventListener('click', markAllAsRead);

const profileButton = document.getElementById('profileButton');
const profileMenu = document.getElementById('profileMenu');

if (profileButton && profileMenu) {
    profileButton.addEventListener('click', function(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
        const notifDropdown = document.getElementById('notificationDropdown');
        if (notifDropdown) notifDropdown.classList.add('hidden');
    });
}

const notifButton = document.getElementById('notificationButton');
const notifDropdown = document.getElementById('notificationDropdown');

if (notifButton && notifDropdown) {
    notifButton.addEventListener('click', function(e) {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
        if (profileMenu) profileMenu.classList.add('hidden');
        if (notifDropdown.classList.contains('hidden') === false) {
            loadNotifications();
        }
    });
}

document.addEventListener('click', function(event) {
    if (profileButton && profileMenu && !profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
        profileMenu.classList.add('hidden');
    }
    if (notifButton && notifDropdown && !notifButton.contains(event.target) && !notifDropdown.contains(event.target)) {
        notifDropdown.classList.add('hidden');
    }
});

setInterval(() => {
    if (notifDropdown && notifDropdown.classList.contains('hidden') === false) {
        loadNotifications();
    } else {
        fetch('{{ route("anggota.notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            });
    }
}, 30000);

function confirmLogout() {
    Swal.fire({
        title: 'Yakin ingin keluar?',
        text: 'Anda akan kembali ke halaman utama',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Mohon tunggu...',
                text: 'Sedang memproses logout',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById('logout-form').submit();
                }
            });
        }
    });
}

const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.querySelector('aside');

if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
    });
}
</script>

<style>
aside {
    transition: transform 0.3s ease-in-out;
}

@media (max-width: 768px) {
    aside {
        transform: translateX(-100%);
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 50;
    }
    aside:not(.-translate-x-full) {
        transform: translateX(0);
    }
}
</style>