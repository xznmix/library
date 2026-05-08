<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            // Field untuk manajemen lisensi digital
            $table->integer('jumlah_lisensi')->default(1)->after('file_path')->comment('Jumlah lisensi digital yang dimiliki');
            $table->integer('lisensi_dipinjam')->default(0)->after('jumlah_lisensi')->comment('Jumlah lisensi sedang dipinjam');
            $table->enum('akses_digital', [
                'online_only', 
                'download_terbatas', 
                'full_access'
            ])->default('online_only')->after('lisensi_dipinjam')->comment('Tipe akses digital');
            $table->integer('durasi_pinjam_hari')->default(7)->after('akses_digital')->comment('Durasi pinjam dalam hari');
            $table->date('tanggal_berlaku_lisensi')->nullable()->after('durasi_pinjam_hari')->comment('Tanggal mulai lisensi berlaku');
            $table->date('tanggal_kadaluarsa_lisensi')->nullable()->after('tanggal_berlaku_lisensi')->comment('Tanggal lisensi kadaluarsa');
            $table->string('penerbit_lisensi')->nullable()->after('tanggal_kadaluarsa_lisensi')->comment('Penerbit/sumber lisensi');
            $table->text('catatan_lisensi')->nullable()->after('penerbit_lisensi')->comment('Catatan lisensi');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_lisensi',
                'lisensi_dipinjam',
                'akses_digital',
                'durasi_pinjam_hari',
                'tanggal_berlaku_lisensi',
                'tanggal_kadaluarsa_lisensi',
                'penerbit_lisensi',
                'catatan_lisensi'
            ]);
        });
    }
};