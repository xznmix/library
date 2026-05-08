<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ========== AUTH CONTROLLERS ==========
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ForcePasswordController;

// ========== ADMIN CONTROLLERS ==========
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\KelolaAkunController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ExportController;

// ========== PETUGAS CONTROLLERS ==========
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\BukuController;
use App\Http\Controllers\Petugas\KatalogController;
use App\Http\Controllers\Petugas\BacaDiTempatController;
use App\Http\Controllers\Petugas\KeanggotaanController;
use App\Http\Controllers\Petugas\SirkulasiController;
use App\Http\Controllers\Petugas\KoleksiDigitalController as PetugasKoleksiDigitalController;
use App\Http\Controllers\Petugas\ReportController;
use App\Http\Controllers\Petugas\KunjunganController as PetugasKunjunganController;
use App\Http\Controllers\Petugas\PaymentController; // TAMBAHKAN INI

// ========== ANGGOTA CONTROLLERS ==========
use App\Http\Controllers\Anggota\AnggotaController;
use App\Http\Controllers\Anggota\KoleksiDigitalController as AnggotaKoleksiDigitalController;
use App\Http\Controllers\Anggota\RiwayatController as AnggotaRiwayatController;
use App\Http\Controllers\Anggota\ProfilController as AnggotaProfilController;
use App\Http\Controllers\Anggota\PeminjamanController as AnggotaPeminjamanController;
use App\Http\Controllers\Anggota\RatingController;
use App\Http\Controllers\Anggota\NotificationController as AnggotaNotificationController;

// ========== KEPALA PUSTAKA CONTROLLERS ==========
use App\Http\Controllers\KepalaPustaka\DashboardController as KepalaPustakaDashboardController;
use App\Http\Controllers\KepalaPustaka\VerifikasiDendaController;
use App\Http\Controllers\KepalaPustaka\AuditBukuController;
use App\Http\Controllers\KepalaPustaka\LaporanController as KepalaPustakaLaporanController;

// ========== PIMPINAN CONTROLLERS ==========
use App\Http\Controllers\Pimpinan\DashboardController as PimpinanDashboardController;
use App\Http\Controllers\Pimpinan\LaporanController as PimpinanLaporanController;
use App\Http\Controllers\Pimpinan\KinerjaController;
use App\Http\Controllers\Pimpinan\ExportController as PimpinanExportController;

// ========== OPAC CONTROLLER (PUBLIC) ==========
use App\Http\Controllers\OPACController;

// ========== DRM CONTROLLERS ==========
use App\Http\Controllers\DigitalCollectionController;
use App\Http\Controllers\DigitalReadController;

// ========== KUNJUNGAN PUBLIC ==========
use App\Http\Controllers\KunjunganController;

// ========== VERIFICATION CONTROLLER ==========
use App\Http\Controllers\Auth\VerificationStatusController;

// ========== HALAMAN UTAMA ==========
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ========== KUNJUNGAN PUBLIC (TANPA LOGIN) ==========
Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
Route::post('/kunjungan/cari-anggota', [KunjunganController::class, 'cariAnggota'])->name('kunjungan.cari');
Route::post('/kunjungan/anggota', [KunjunganController::class, 'storeAnggota'])->name('kunjungan.anggota');
Route::post('/kunjungan/pemustaka', [KunjunganController::class, 'storePemustaka'])->name('kunjungan.pemustaka');

// ========== OPAC (PUBLIC - TANPA LOGIN) ==========
Route::get('/opac', [OPACController::class, 'index'])->name('opac.index');
Route::get('/opac/{id}', [OPACController::class, 'show'])->name('opac.show');
Route::get('/opac/download/{id}', [OPACController::class, 'download'])->name('opac.download');
Route::get('/opac/ai/search', [OPACController::class, 'searchWithAI'])->name('opac.ai.search');

// ========== AUTHENTICATION ROUTES ==========
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');
    
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// Register routes
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store'])->name('register.store');
Route::get('register/confirm', [RegisteredUserController::class, 'confirm'])->name('register.confirm');
Route::post('register/submit', [RegisteredUserController::class, 'submit'])->name('register.submit');
Route::get('register/pending', [RegisteredUserController::class, 'pending'])->name('register.pending');
Route::get('verify-email/{token}', [RegisteredUserController::class, 'verifyEmail'])->name('register.verify');

// ========== VERIFICATION STATUS ROUTES (PUBLIC) ==========
Route::get('/verifikasi/cek', [VerificationStatusController::class, 'showCheckForm'])->name('verification.check.form');
Route::post('/verifikasi/cek', [VerificationStatusController::class, 'checkStatus'])->name('verification.check.submit');
Route::get('/verifikasi/status/{email}', [VerificationStatusController::class, 'showStatus'])->name('verification.status');
Route::post('/verifikasi/cek/ajax', [VerificationStatusController::class, 'checkStatusAjax'])->name('verification.check.ajax');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ========== DASHBOARD REDIRECT ==========
Route::get('/dashboard', function () {
    $user = Auth::user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'petugas') {
        return redirect()->route('petugas.dashboard');
    } elseif (in_array($user->role, ['siswa', 'guru', 'pegawai', 'umum'])) {
        return redirect()->route('anggota.dashboard');
    } elseif ($user->role === 'kepala_pustaka') {
        return redirect()->route('kepala-pustaka.dashboard');
    } elseif ($user->role === 'pimpinan') {
        return redirect()->route('pimpinan.dashboard');
    }
    return redirect('/');
})->middleware('auth')->name('dashboard');

// ========== DIGITAL STREAMING ROUTE (AUTH) ==========
Route::get('/digital/stream/{id}', [PetugasKoleksiDigitalController::class, 'stream'])
    ->name('digital.stream')
    ->middleware(['auth']);

// ========== DIGITAL ACCESS ROUTES ==========
Route::middleware(['auth'])->group(function () {
    Route::get('/digital/read', [DigitalReadController::class, 'read'])->name('digital.read');
    Route::post('/digital/return/{id}', [DigitalReadController::class, 'return'])->name('digital.return');
});

// ========== ADMIN ROUTES ==========
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/activities', [AdminController::class, 'getActivityLogs'])->name('dashboard.activities');
    Route::get('/dashboard/chart-data', [AdminController::class, 'getChartDataApi'])->name('dashboard.chart');
    
    Route::prefix('kelola-akun')->name('kelola-akun.')->group(function () {
        Route::get('/', [KelolaAkunController::class, 'index'])->name('index');
        Route::get('/create', [KelolaAkunController::class, 'create'])->name('create');
        Route::post('/', [KelolaAkunController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KelolaAkunController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KelolaAkunController::class, 'update'])->name('update');
        Route::delete('/{id}', [KelolaAkunController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reset', [KelolaAkunController::class, 'resetPassword'])->name('reset');
        Route::post('/import', [KelolaAkunController::class, 'import'])->name('import');
        Route::get('/template', [KelolaAkunController::class, 'downloadTemplate'])->name('template');
    });
    
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-akun', [LaporanController::class, 'exportAkun'])->name('export-akun');
    });
    
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/users/excel', [ExportController::class, 'usersExcel'])->name('users.excel');
        Route::get('/users/pdf', [ExportController::class, 'usersPdf'])->name('users.pdf');
        Route::get('/activities/excel', [ExportController::class, 'activitiesExcel'])->name('activities.excel');
        Route::get('/activities/pdf', [ExportController::class, 'activitiesPdf'])->name('activities.pdf');
        Route::get('/report/pdf', [ExportController::class, 'reportPdf'])->name('report.pdf');
    });
    
    Route::get('/force-change-password', [ForcePasswordController::class, 'index'])->name('password.force');
    Route::post('/force-change-password', [ForcePasswordController::class, 'update'])->name('password.force.update');
});

// ========== PETUGAS ROUTES ==========
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/realtime-data', [PetugasDashboardController::class, 'getRealtimeData'])->name('realtime');

    // BUKU ROUTES with IMPORT
    Route::prefix('buku')->name('buku.')->group(function () {
        Route::get('/', [BukuController::class, 'index'])->name('index');
        Route::get('/create', [BukuController::class, 'create'])->name('create');
        Route::post('/', [BukuController::class, 'store'])->name('store');
        
        // ⚠️ PENTING: Route download-template HARUS sebelum route {id}!
        Route::get('/download-template', [BukuController::class, 'downloadTemplate'])->name('download-template');
        Route::get('/download-template-xlsx', [BukuController::class, 'downloadTemplateXlsx'])->name('download-template-xlsx');
        
        Route::post('/import', [BukuController::class, 'import'])->name('import');
        Route::post('/scan-barcode', [BukuController::class, 'scanBarcode'])->name('scan-barcode');
        
        // Route {id} dan turunannya harus di paling bawah
        Route::get('/{id}', [BukuController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BukuController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BukuController::class, 'update'])->name('update');
        Route::delete('/{id}', [BukuController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('katalog')->name('katalog.')->group(function () {
        Route::get('/', [KatalogController::class, 'index'])->name('index');
        Route::get('/print', [KatalogController::class, 'print'])->name('print');
        Route::get('/print-card/{id}', [KatalogController::class, 'printCard'])->name('print-card');
        Route::get('/print-multiple', [KatalogController::class, 'printMultiple'])->name('print-multiple');
    });

    Route::prefix('baca-ditempat')->name('baca-ditempat.')->group(function () {
        Route::get('/', [BacaDiTempatController::class, 'index'])->name('index');
        Route::get('/create', [BacaDiTempatController::class, 'create'])->name('create');
        Route::post('/', [BacaDiTempatController::class, 'store'])->name('store');
        Route::get('/cari-anggota', [BacaDiTempatController::class, 'cariAnggota'])->name('cari-anggota');
        Route::get('/cari-buku', [BacaDiTempatController::class, 'cariBuku'])->name('cari-buku');
        Route::get('/{id}', [BacaDiTempatController::class, 'show'])->name('show');
        Route::post('/{id}/selesai', [BacaDiTempatController::class, 'selesai'])->name('selesai');
    });

    Route::get('/keanggotaan', [KeanggotaanController::class, 'index'])->name('keanggotaan.index');
    Route::get('/keanggotaan/create', [KeanggotaanController::class, 'create'])->name('keanggotaan.create');
    Route::post('/keanggotaan', [KeanggotaanController::class, 'store'])->name('keanggotaan.store');
    Route::get('/keanggotaan/{id}', [KeanggotaanController::class, 'show'])->name('keanggotaan.show');
    Route::get('/keanggotaan/{id}/edit', [KeanggotaanController::class, 'edit'])->name('keanggotaan.edit');
    Route::put('/keanggotaan/{id}', [KeanggotaanController::class, 'update'])->name('keanggotaan.update');
    Route::post('/keanggotaan/{id}/approve', [KeanggotaanController::class, 'approve'])->name('keanggotaan.approve');
    Route::post('/keanggotaan/{id}/reject', [KeanggotaanController::class, 'reject'])->name('keanggotaan.reject');
    Route::post('/keanggotaan/{id}/deactivate', [KeanggotaanController::class, 'deactivate'])->name('keanggotaan.deactivate');
    Route::post('/keanggotaan/{id}/activate', [KeanggotaanController::class, 'activate'])->name('keanggotaan.activate');
    Route::get('/keanggotaan/export', [KeanggotaanController::class, 'export'])->name('keanggotaan.export');

    Route::prefix('kunjungan')->name('kunjungan.')->group(function () {
        Route::get('/', [PetugasKunjunganController::class, 'index'])->name('index');
        Route::get('/{id}', [PetugasKunjunganController::class, 'show'])->name('show');
        Route::delete('/{id}', [PetugasKunjunganController::class, 'destroy'])->name('destroy');
        Route::get('/rekap/data', [PetugasKunjunganController::class, 'rekap'])->name('rekap');
    });

    // ===== SIRKULASI =====
    Route::prefix('sirkulasi')->name('sirkulasi.')->group(function () {
        
        Route::get('/peminjaman', [SirkulasiController::class, 'indexPeminjaman'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [SirkulasiController::class, 'createPeminjaman'])->name('peminjaman.create');
        Route::post('/peminjaman', [SirkulasiController::class, 'storePeminjaman'])->name('peminjaman.store');
        Route::get('/peminjaman/{id}', [SirkulasiController::class, 'showPeminjaman'])->name('peminjaman.show');
        Route::get('/peminjaman/{id}/json', [SirkulasiController::class, 'getPeminjamanJson'])->name('peminjaman.json');

        Route::post('/peminjaman/{id}/perpanjang', [SirkulasiController::class, 'perpanjangPeminjaman'])->name('peminjaman.perpanjang');

        Route::get('/cari-anggota', [SirkulasiController::class, 'cariAnggota'])->name('cari-anggota');
        Route::get('/cari-buku', [SirkulasiController::class, 'cariBuku'])->name('cari-buku');
        Route::get('/get-all-anggota', [SirkulasiController::class, 'getAllAnggota'])->name('get-all-anggota');
        Route::get('/get-all-buku', [SirkulasiController::class, 'getAllBuku'])->name('get-all-buku');
        Route::get('/get-anggota/{id}', [SirkulasiController::class, 'getAnggota'])->name('get-anggota');
        Route::get('/get-buku/{id}', [SirkulasiController::class, 'getBuku'])->name('get-buku');
        
        Route::get('/pengembalian', [SirkulasiController::class, 'indexPengembalian'])->name('pengembalian.index');
        Route::get('/cari-peminjaman', [SirkulasiController::class, 'cariPeminjaman'])->name('cari-peminjaman');
        Route::post('/pengembalian/proses', [SirkulasiController::class, 'prosesPengembalian'])->name('pengembalian.proses');
        
        Route::get('/digital/read/{token}', [DigitalCollectionController::class, 'reader'])->name('digital.reader');
        
        Route::get('/riwayat', [SirkulasiController::class, 'riwayat'])->name('riwayat');

        Route::prefix('denda')->name('denda.')->group(function () {
            Route::get('/', [SirkulasiController::class, 'indexDenda'])->name('index');
            Route::get('/lunas', [SirkulasiController::class, 'indexDendaLunas'])->name('lunas');
        });

        // PEMBAYARAN DENDA
        Route::get('pembayaran/{id}', [SirkulasiController::class, 'showPembayaran'])->name('pembayaran.show');
        Route::post('pembayaran/{id}/confirm', [SirkulasiController::class, 'confirmPembayaran'])->name('pembayaran.confirm');
    });

    // QRIS ROUTES
    Route::prefix('qris')->name('qris.')->group(function () {
        Route::get('generate/{id_denda}', [PaymentController::class, 'generateQRIS'])->name('generate');
        Route::get('status', [PaymentController::class, 'checkStatus'])->name('status');
    });

    // ===== KOLEKSI DIGITAL =====
    Route::prefix('koleksi-digital')->name('koleksi-digital.')->group(function () {
        Route::get('/', [PetugasKoleksiDigitalController::class, 'index'])->name('index');
        Route::get('/create', [PetugasKoleksiDigitalController::class, 'create'])->name('create');
        Route::post('/', [PetugasKoleksiDigitalController::class, 'store'])->name('store');
        Route::get('/{id}', [PetugasKoleksiDigitalController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PetugasKoleksiDigitalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PetugasKoleksiDigitalController::class, 'update'])->name('update');
        Route::delete('/{id}', [PetugasKoleksiDigitalController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/baca', [PetugasKoleksiDigitalController::class, 'baca'])->name('baca');
        Route::get('/{id}/download', [PetugasKoleksiDigitalController::class, 'download'])->name('download');
    });

    Route::get('/digital/stream/{id}', [PetugasKoleksiDigitalController::class, 'stream'])->name('digital.stream');
    Route::get('/digital/download/{id}', [PetugasKoleksiDigitalController::class, 'download'])->name('digital.download');

    // ===== REPORT =====
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/peminjaman', [ReportController::class, 'peminjaman'])->name('peminjaman');
        Route::get('/anggota', [ReportController::class, 'anggota'])->name('anggota');
        Route::get('/buku', [ReportController::class, 'buku'])->name('buku');
        Route::get('/kunjungan', [ReportController::class, 'kunjungan'])->name('kunjungan');
        Route::get('/denda', [ReportController::class, 'denda'])->name('denda');
        
        Route::get('/peminjaman/export-pdf', [ReportController::class, 'exportPeminjamanPdf'])->name('peminjaman.export.pdf');
        Route::get('/anggota/export-pdf', [ReportController::class, 'exportAnggotaPdf'])->name('anggota.export.pdf');
        Route::get('/buku/export-pdf', [ReportController::class, 'exportBukuPdf'])->name('buku.export.pdf');
        Route::get('/kunjungan/export-pdf', [ReportController::class, 'exportKunjunganPdf'])->name('kunjungan.export.pdf');
        Route::get('/denda/export-pdf', [ReportController::class, 'exportDendaPdf'])->name('denda.export.pdf');
        Route::get('/export-all-pdf', [ReportController::class, 'exportAllPdf'])->name('export.all.pdf');
        
        Route::get('/peminjaman/export-excel', [ReportController::class, 'exportPeminjamanExcel'])->name('peminjaman.export.excel');
        Route::get('/anggota/export-excel', [ReportController::class, 'exportAnggotaExcel'])->name('anggota.export.excel');
        Route::get('/buku/export-excel', [ReportController::class, 'exportBukuExcel'])->name('buku.export.excel');
        Route::get('/kunjungan/export-excel', [ReportController::class, 'exportKunjunganExcel'])->name('kunjungan.export.excel');
        Route::get('/denda/export-excel', [ReportController::class, 'exportDendaExcel'])->name('denda.export.excel');
        Route::get('/export-all-excel', [ReportController::class, 'exportAllExcel'])->name('export.all.excel');
    });

    // Booking Management
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Petugas\BookingController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Petugas\BookingController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Petugas\BookingController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Petugas\BookingController::class, 'reject'])->name('reject');
        Route::post('/{id}/process-pickup', [\App\Http\Controllers\Petugas\BookingController::class, 'processPickup'])->name('process-pickup');
        Route::post('/{id}/expire', [\App\Http\Controllers\Petugas\BookingController::class, 'expire'])->name('expire');
    });
}); // <-- TUTUP GROUP PETUGAS

// Webhook Midtrans (tanpa auth - dipanggil dari Midtrans)
Route::post('midtrans/notification', [PaymentController::class, 'handleNotification'])->name('midtrans.notification');

// ========== ANGGOTA ROUTES ==========
Route::prefix('anggota')
    ->name('anggota.')
    ->middleware(['auth', 'role:siswa,guru,pegawai,umum'])
    ->group(function () {
    
    Route::get('/dashboard', [AnggotaController::class, 'dashboard'])->name('dashboard');
    
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::post('/store', [AnggotaPeminjamanController::class, 'store'])->name('store');
        Route::get('/riwayat', [AnggotaPeminjamanController::class, 'riwayat'])->name('riwayat');
        Route::get('/riwayat/{id}', [AnggotaPeminjamanController::class, 'detail'])->name('riwayat.detail');
    });
    
    // ✅ KOLEKSI DIGITAL - LENGKAP DENGAN ROUTE PINJAM
    Route::prefix('koleksi-digital')->name('koleksi-digital.')->group(function () {
        Route::get('/', [AnggotaKoleksiDigitalController::class, 'index'])->name('index');
        Route::get('/{id}', [AnggotaKoleksiDigitalController::class, 'show'])->name('show');
        Route::post('/{id}/pinjam', [AnggotaKoleksiDigitalController::class, 'pinjam'])->name('pinjam');
        Route::get('/{id}/download', [AnggotaKoleksiDigitalController::class, 'download'])->name('download');
        Route::get('/{id}/baca', [AnggotaKoleksiDigitalController::class, 'baca'])->name('baca');
    });
    
    Route::get('/riwayat', [AnggotaRiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [AnggotaRiwayatController::class, 'show'])->name('riwayat.show');

    Route::prefix('rating')->name('rating.')->group(function () {
        Route::post('/{bukuId}', [RatingController::class, 'store'])->name('store');
        Route::put('/{bukuId}', [RatingController::class, 'update'])->name('update');
        Route::delete('/{bukuId}', [RatingController::class, 'destroy'])->name('destroy');
        Route::get('/{bukuId}/get', [RatingController::class, 'getRating'])->name('get');
        Route::get('/create/{peminjamanId}', [RatingController::class, 'create'])->name('create');
        Route::get('/buku/{bukuId}/rate', [RatingController::class, 'createFromBuku'])->name('from-buku');
    });
    
    Route::get('/profil', [AnggotaProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [AnggotaProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/upload-foto', [AnggotaProfilController::class, 'uploadFoto'])->name('profil.upload-foto');
    Route::post('/profil/change-password', [AnggotaProfilController::class, 'changePassword'])->name('profil.change-password');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [AnggotaNotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [AnggotaNotificationController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/latest', [AnggotaNotificationController::class, 'getLatest'])->name('latest');
        Route::post('/{id}/read', [AnggotaNotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [AnggotaNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [AnggotaNotificationController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Anggota\BookingController::class, 'index'])->name('index');
        Route::get('/create/{bukuId}', [\App\Http\Controllers\Anggota\BookingController::class, 'create'])->name('create');
        Route::post('/store/{bukuId}', [\App\Http\Controllers\Anggota\BookingController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Anggota\BookingController::class, 'show'])->name('show');
        Route::delete('/{id}/cancel', [\App\Http\Controllers\Anggota\BookingController::class, 'cancel'])->name('cancel');
    });
});

// ========== KEPALA PUSTAKA ROUTES ==========
Route::prefix('kepala-pustaka')
    ->name('kepala-pustaka.')
    ->middleware(['auth', 'role:kepala_pustaka'])
    ->group(function () {
        
    Route::get('/dashboard', [KepalaPustakaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/realtime-data', [KepalaPustakaDashboardController::class, 'getRealtimeData'])->name('realtime');
    
    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiDendaController::class, 'index'])->name('index');
        Route::get('/{id}', [VerifikasiDendaController::class, 'show'])->name('detail');
        Route::post('/{id}', [VerifikasiDendaController::class, 'verifikasi'])->name('proses');
        Route::post('/massal', [VerifikasiDendaController::class, 'verifikasiMassal'])->name('massal');
    });

    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/buku', [AuditBukuController::class, 'index'])->name('buku');
        Route::get('/buku/{id}', [AuditBukuController::class, 'detail'])->name('buku.detail');
        Route::get('/stock-opname', [AuditBukuController::class, 'stockOpnamePage'])->name('stock-opname-page');
        Route::post('/stock-opname/proses', [AuditBukuController::class, 'stockOpname'])->name('stock-opname.proses');
        Route::post('/buku/{id}/update-kondisi', [AuditBukuController::class, 'updateKondisi'])->name('buku.update-kondisi');
        Route::get('/export', [AuditBukuController::class, 'export'])->name('export');
        Route::put('/queue/{id}/status', [AuditBukuController::class, 'updateAuditStatus'])->name('queue.update-status');     
    });
    
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/denda', [KepalaPustakaLaporanController::class, 'denda'])->name('denda');
        Route::get('/denda/export-excel', [KepalaPustakaLaporanController::class, 'exportDendaExcel'])->name('denda.export-excel');
        Route::get('/denda/export-pdf', [KepalaPustakaLaporanController::class, 'exportDendaPdf'])->name('denda.export-pdf');
        
        Route::get('/aktivitas', [KepalaPustakaLaporanController::class, 'aktivitas'])->name('aktivitas');
        Route::get('/aktivitas/export', [KepalaPustakaLaporanController::class, 'exportAktivitas'])->name('aktivitas.export');

        Route::get('/peminjaman', [KepalaPustakaLaporanController::class, 'peminjaman'])->name('peminjaman');
        Route::get('/peminjaman/export-excel', [KepalaPustakaLaporanController::class, 'exportPeminjamanExcel'])->name('peminjaman.export-excel');
        Route::get('/peminjaman/export-pdf', [KepalaPustakaLaporanController::class, 'exportPeminjamanPdf'])->name('peminjaman.export-pdf');
        
        Route::get('/kunjungan', [KepalaPustakaLaporanController::class, 'kunjungan'])->name('kunjungan');
        Route::get('/kunjungan/export-excel', [KepalaPustakaLaporanController::class, 'exportKunjunganExcel'])->name('kunjungan.export-excel');
    });
    
    Route::get('/statistik-petugas', [VerifikasiDendaController::class, 'statistikPetugas'])->name('statistik-petugas');
});

// ========== PIMPINAN ROUTES ==========
Route::prefix('pimpinan')
    ->name('pimpinan.')
    ->middleware(['auth', 'role:pimpinan'])
    ->group(function () {
        
    Route::get('/dashboard', [PimpinanDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/peminjaman', [PimpinanLaporanController::class, 'peminjaman'])->name('peminjaman');
        Route::get('/kunjungan', [PimpinanLaporanController::class, 'kunjungan'])->name('kunjungan');
        Route::get('/keuangan', [PimpinanLaporanController::class, 'keuangan'])->name('keuangan');
    });
    
    Route::get('/kinerja', [KinerjaController::class, 'index'])->name('kinerja.index');
    
    Route::get('/export', [PimpinanExportController::class, 'index'])->name('export.index');
    Route::get('/export/download/{jenis}/{format}', [PimpinanExportController::class, 'download'])->name('export.download');
});  

// ========== FALLBACK ROUTE (404) ==========
Route::fallback(function () {
    return view('errors.404');
});