@extends('petugas.layouts.app')

@section('title', 'Pembayaran Denda QRIS')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title mb-0">Pembayaran Denda - QRIS</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-info text-white p-3 rounded">
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Denda</span>
                                    <span class="info-box-number h3">Rp {{ number_format($denda->jumlah_denda, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama Anggota</th>
                                    <td>{{ optional($denda->anggota)->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Anggota</th>
                                    <td>{{ $denda->anggota->no_anggota ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Judul Buku</th>
                                    <td>{{ optional(optional($denda->peminjaman)->buku)->judul ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $denda->keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <div id="payment-section" class="text-center">
                                <div id="payment-loading" class="py-5">
                                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                                    <p class="mt-3">Memproses pembayaran...</p>
                                </div>
                                <div id="payment-success" style="display: none;">
                                    <div class="alert alert-success p-4">
                                        <i class="fas fa-check-circle fa-4x mb-3"></i>
                                        <h4>Pembayaran Berhasil!</h4>
                                        <p>Mengalihkan ke halaman daftar denda...</p>
                                    </div>
                                </div>
                                <div id="payment-failed" style="display: none;">
                                    <div class="alert alert-danger p-4">
                                        <i class="fas fa-times-circle fa-4x mb-3"></i>
                                        <h4>Pembayaran Gagal!</h4>
                                        <p id="error-message"></p>
                                        <button onclick="window.location.href='{{ route('petugas.sirkulasi.denda.index') }}'" class="btn btn-primary mt-3">
                                            Kembali ke Daftar Denda
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <small>
                                    <strong>Informasi:</strong> Pembayaran akan diproses oleh Midtrans. 
                                    Dana akan masuk ke rekening merchant yang terdaftar di Midtrans.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
let statusCheckInterval = null;

$(document).ready(function() {
    initPayment();
});

function initPayment() {
    $.ajax({
        url: '{{ route("petugas.sirkulasi.payment.qris", $denda->id_denda) }}',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                if (response.existing) {
                    // Cek status transaksi yang sudah ada
                    checkPaymentStatus();
                } else {
                    // Buka snap popup
                    snap.pay(response.token, {
                        onSuccess: function(result) {
                            handlePaymentSuccess(result);
                        },
                        onPending: function(result) {
                            startStatusCheck();
                        },
                        onError: function(result) {
                            handlePaymentError(result.status_message || 'Pembayaran gagal');
                        },
                        onClose: function() {
                            startStatusCheck();
                        }
                    });
                }
            } else {
                handlePaymentError(response.message || 'Gagal memproses pembayaran');
            }
        },
        error: function(xhr) {
            handlePaymentError('Terjadi kesalahan. Silakan coba lagi.');
        }
    });
}

function startStatusCheck() {
    if (statusCheckInterval) clearInterval(statusCheckInterval);
    
    statusCheckInterval = setInterval(function() {
        checkPaymentStatus();
    }, 3000);
    
    // Stop setelah 5 menit
    setTimeout(function() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
            if ($('#payment-loading').is(':visible')) {
                handlePaymentError('Waktu pembayaran habis. Silakan coba lagi.');
            }
        }
    }, 300000);
}

function checkPaymentStatus() {
    $.ajax({
        url: '{{ route("petugas.sirkulasi.payment.status", $denda->id_denda) }}',
        method: 'GET',
        success: function(response) {
            if (response.paid === true) {
                if (statusCheckInterval) clearInterval(statusCheckInterval);
                handlePaymentSuccess();
            }
        },
        error: function() {
            // Silent error, continue checking
        }
    });
}

function handlePaymentSuccess(result) {
    $('#payment-loading').hide();
    $('#payment-success').show();
    
    setTimeout(function() {
        window.location.href = '{{ route("petugas.sirkulasi.denda.index") }}';
    }, 3000);
}

function handlePaymentError(message) {
    $('#payment-loading').hide();
    $('#payment-failed').show();
    $('#error-message').text(message);
    
    if (statusCheckInterval) clearInterval(statusCheckInterval);
}
</script>
@endpush