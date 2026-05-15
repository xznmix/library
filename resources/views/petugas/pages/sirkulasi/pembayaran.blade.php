@extends('petugas.layouts.app')

@section('title', 'Pembayaran Denda')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6">
    
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Pembayaran Denda</h1>
        <p class="text-sm text-gray-500 mt-1">Pilih metode pembayaran untuk menyelesaikan denda</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        
        {{-- Informasi Denda --}}
        <div class="space-y-4">
            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-5 border border-orange-200">
                <h3 class="font-semibold text-gray-800 mb-3">Detail Denda</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center pb-2 border-b border-orange-100">
                        <span class="text-gray-600">Jumlah Denda</span>
                        <span class="text-2xl font-bold text-orange-600">{{ $denda->formatted_amount }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold 
                            @if($denda->isPaid()) bg-green-100 text-green-800
                            @elseif($denda->isPending()) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $denda->status ?? 'belum_bayar' }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">Informasi Peminjam</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nama</span>
                        <span class="font-medium">{{ $denda->anggota->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">No. Anggota</span>
                        <span>{{ $denda->anggota->no_anggota ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Buku</span>
                        <span>{{ $denda->peminjaman->buku->judul ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Pilihan Metode --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <div class="flex">
                    <button onclick="showMethod('tunai')" id="tab-tunai" 
                        class="flex-1 py-3 px-4 text-center font-medium transition-all border-b-2 border-blue-500 text-blue-600">
                        💰 Tunai
                    </button>
                    <button onclick="showMethod('transfer')" id="tab-transfer" 
                        class="flex-1 py-3 px-4 text-center font-medium transition-all border-b-2 border-transparent hover:text-gray-600">
                        🏦 Transfer
                    </button>
                    <button onclick="showMethod('qris')" id="tab-qris" 
                        class="flex-1 py-3 px-4 text-center font-medium transition-all border-b-2 border-transparent hover:text-gray-600">
                        📱 QRIS
                    </button>
                </div>
            </div>
            
            <div class="p-5">
                <!-- Tunai -->
                <div id="method-tunai" class="method-content text-center py-8">
                    <div class="text-6xl mb-4">💰</div>
                    <h3 class="text-lg font-semibold mb-2">Pembayaran Tunai</h3>
                    <p class="text-gray-500 mb-4">Konfirmasi pembayaran tunai oleh petugas</p>
                    <button onclick="confirmPayment('tunai')" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Konfirmasi Pembayaran
                    </button>
                </div>
                
                <!-- Transfer -->
                <div id="method-transfer" class="method-content text-center py-8 hidden">
                    <div class="text-6xl mb-4">🏦</div>
                    <h3 class="text-lg font-semibold mb-2">Transfer Bank</h3>
                    <p class="text-gray-500 mb-2">BCA: 1234567890 a.n Perpustakaan</p>
                    <p class="text-gray-500 mb-4">Mandiri: 9876543210 a.n Perpustakaan</p>
                    <button onclick="confirmPayment('transfer')" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Konfirmasi Pembayaran
                    </button>
                </div>
                
                <!-- QRIS -->
                <div id="method-qris" class="method-content text-center py-4 hidden">
                    <div id="qrisContainer">
                        <div class="text-6xl mb-4">📱</div>
                        <h3 class="text-lg font-semibold mb-2">Scan QRIS untuk Membayar</h3>
                        <p class="text-gray-500 mb-4">Jumlah: {{ $denda->formatted_amount }}</p>
                        
                        {{-- Loading --}}
                        <div id="qrisLoading" class="py-8 hidden">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="mt-2 text-gray-500">Menyiapkan QRIS...</p>
                        </div>
                        
                        {{-- QR tampil --}}
                        <div id="qrisImage" class="hidden">
                            <div id="qrCodeDisplay" 
                                class="bg-white p-4 rounded-lg shadow-lg inline-flex items-center justify-center"
                                style="min-width:264px; min-height:264px;">
                            </div>
                            <div class="mt-4">
                                <button onclick="checkPaymentStatus()" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Cek Status Pembayaran
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2" id="qrisExpiry">Kode QR berlaku 30 menit</p>
                        </div>
                        
                        {{-- Sukses --}}
                        <div id="qrisPaid" class="hidden py-8">
                            <div class="text-green-600 text-6xl mb-2">✓</div>
                            <p class="font-semibold text-green-600 text-lg">Pembayaran Berhasil!</p>
                            <p class="text-sm text-gray-500 mt-2">Mengalihkan ke halaman daftar denda...</p>
                        </div>

                        {{-- Error --}}
                        <div id="qrisError" class="hidden py-8">
                            <div class="text-red-500 text-5xl mb-3">⚠️</div>
                            <p class="font-semibold text-red-600" id="qrisErrorMsg">Gagal memuat QRIS</p>
                            <button onclick="retryQRIS()" 
                                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- qrcode.js sebagai fallback jika URL gambar dari Midtrans gagal load --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
let currentOrderId  = null;
let currentQrString = null;
let statusCheckInterval = null;

// ✅ Semua URL dari Blade disimpan ke konstanta — mencegah undefined di JS
const GENERATE_URL = '{{ route("petugas.qris.generate", $denda->id) }}';
const STATUS_URL   = '{{ route("petugas.qris.status") }}';
const DENDA_INDEX  = '{{ route("petugas.sirkulasi.denda.index") }}';
const CONFIRM_URL  = '{{ route("petugas.sirkulasi.pembayaran.confirm", $denda->id) }}';
const CSRF_TOKEN   = '{{ csrf_token() }}';

// ─── Tab switching ────────────────────────────────────────────────────────────

function showMethod(method) {
    document.querySelectorAll('.method-content').forEach(el => el.classList.add('hidden'));
    document.getElementById(`method-${method}`).classList.remove('hidden');
    
    ['tunai', 'transfer', 'qris'].forEach(m => {
        const tab = document.getElementById(`tab-${m}`);
        if (m === method) {
            tab.classList.add('border-blue-500', 'text-blue-600');
            tab.classList.remove('border-transparent');
        } else {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent');
        }
    });
    
    if (method === 'qris' && !currentOrderId) {
        initQRIS();
    }
}

// ─── Init QRIS ────────────────────────────────────────────────────────────────

function retryQRIS() {
    currentOrderId  = null;
    currentQrString = null;
    initQRIS();
}

async function initQRIS() {
    // Reset semua state UI
    document.getElementById('qrisLoading').classList.remove('hidden');
    document.getElementById('qrisImage').classList.add('hidden');
    document.getElementById('qrisPaid').classList.add('hidden');
    document.getElementById('qrisError').classList.add('hidden');
    document.getElementById('qrCodeDisplay').innerHTML = '';

    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
        statusCheckInterval = null;
    }

    try {
        const response = await fetch(GENERATE_URL);

        if (!response.ok) {
            throw new Error(`HTTP error ${response.status}`);
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Gagal generate QRIS');
        }

        currentOrderId  = data.order_id;
        currentQrString = data.qr_string;

        // Tampilkan waktu expiry jika ada
        if (data.expiry) {
            const expiry = new Date(data.expiry.replace(' ', 'T'));
            document.getElementById('qrisExpiry').textContent =
                `QR berlaku hingga ${expiry.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
        }

        // ✅ Render QR: coba URL gambar Midtrans dulu, fallback ke qrcode.js
        if (data.qr_image) {
            renderQRFromImage(data.qr_image, data.qr_string);
        } else {
            renderQRFromString(data.qr_string);
        }

        document.getElementById('qrisLoading').classList.add('hidden');
        document.getElementById('qrisImage').classList.remove('hidden');

        // Auto cek status tiap 3 detik
        statusCheckInterval = setInterval(checkPaymentStatus, 3000);

        // Stop & expired setelah 30 menit
        setTimeout(() => {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
                document.getElementById('qrisImage').classList.add('hidden');
                document.getElementById('qrisError').classList.remove('hidden');
                document.getElementById('qrisErrorMsg').textContent =
                    'QRIS sudah expired. Klik "Coba Lagi" untuk generate baru.';
                currentOrderId = null;
            }
        }, 30 * 60 * 1000);

    } catch (error) {
        console.error('initQRIS error:', error);
        document.getElementById('qrisLoading').classList.add('hidden');
        document.getElementById('qrisError').classList.remove('hidden');
        document.getElementById('qrisErrorMsg').textContent = error.message || 'Gagal memuat QRIS';
    }
}

// ✅ Render dari URL gambar Midtrans (langsung, tidak perlu library)
function renderQRFromImage(imageUrl, fallbackQrString) {
    const container = document.getElementById('qrCodeDisplay');
    const img = document.createElement('img');
    img.src       = imageUrl;
    img.alt       = 'QRIS';
    img.className = 'w-64 h-64 mx-auto';
    img.onerror   = () => {
        // Fallback ke qrcode.js jika gambar gagal (misal CORS di localhost)
        console.warn('QR image gagal load, fallback ke qrcode.js');
        renderQRFromString(fallbackQrString);
    };
    container.appendChild(img);
}

// ✅ Render dari qr_string menggunakan qrcode.js (fallback)
function renderQRFromString(qrString) {
    const container = document.getElementById('qrCodeDisplay');
    container.innerHTML = '';
    new QRCode(container, {
        text: qrString,
        width: 256,
        height: 256,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
}

// ─── Cek Status Pembayaran ────────────────────────────────────────────────────

async function checkPaymentStatus() {
    if (!currentOrderId) return;

    try {
        const response = await fetch(`${STATUS_URL}?order_id=${currentOrderId}`);
        const data     = await response.json();

        if (data.success && data.status === 'paid') {
            if (statusCheckInterval) {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
            }

            document.getElementById('qrisImage').classList.add('hidden');
            document.getElementById('qrisPaid').classList.remove('hidden');

            setTimeout(() => {
                window.location.href = DENDA_INDEX;
            }, 2000);
        }
    } catch (error) {
        console.error('Check status error:', error);
    }
}

// ─── Konfirmasi Tunai / Transfer ──────────────────────────────────────────────

async function confirmPayment(metode) {
    const result = await Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: `Apakah pembayaran dengan metode ${metode.toUpperCase()} sudah diterima?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Sudah Dibayar!'
    });

    if (!result.isConfirmed) return;

    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const response = await fetch(CONFIRM_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({ metode })
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500 })
                .then(() => window.location.href = DENDA_INDEX);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire('Gagal!', error.message, 'error');
    }
}

// ─── Auto buka tab QRIS jika status pending ───────────────────────────────────
@if($denda->payment_status === 'pending')
    document.addEventListener('DOMContentLoaded', () => showMethod('qris'));
@endif
</script>

<style>
    .method-content { transition: all 0.3s ease; }
</style>
@endsection