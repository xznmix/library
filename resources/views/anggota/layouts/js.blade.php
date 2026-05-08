<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- SweetAlert2 untuk konfirmasi logout --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Fungsi konfirmasi logout
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: 'Anda akan kembali ke halaman utama',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
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
                        
                        // Cari form logout dan submit
                        const logoutForm = document.getElementById('logout-form') || document.getElementById('sidebarLogoutForm');
                        if (logoutForm) {
                            logoutForm.submit();
                        }
                    }
                });
            }
        });
    }
</script>