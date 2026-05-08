<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'petugas_id')) {
                $table->foreignId('petugas_id')->nullable()->constrained('users')->after('user_id');
            }
            if (!Schema::hasColumn('peminjaman', 'status_verifikasi')) {
                $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])
                      ->default('pending')->after('status_pinjam');
            }
            if (!Schema::hasColumn('peminjaman', 'diverifikasi_oleh')) {
                $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->after('status_verifikasi');
            }
            if (!Schema::hasColumn('peminjaman', 'diverifikasi_at')) {
                $table->timestamp('diverifikasi_at')->nullable()->after('diverifikasi_oleh');
            }
            if (!Schema::hasColumn('peminjaman', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('diverifikasi_at');
            }
            if (!Schema::hasColumn('peminjaman', 'denda_rusak')) {
                $table->decimal('denda_rusak', 10, 2)->default(0)->after('denda');
            }
            if (!Schema::hasColumn('peminjaman', 'denda_total')) {
                $table->decimal('denda_total', 10, 2)->default(0)->after('denda_rusak');
            }
            if (!Schema::hasColumn('peminjaman', 'denda_asli')) {
                $table->decimal('denda_asli', 10, 2)->nullable()->after('denda_total');
            }
            if (!Schema::hasColumn('peminjaman', 'kondisi_kembali')) {
                $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])
                      ->default('baik')->after('denda_asli');
            }
            if (!Schema::hasColumn('peminjaman', 'catatan_kondisi')) {
                $table->text('catatan_kondisi')->nullable()->after('kondisi_kembali');
            }
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'petugas_id',
                'status_verifikasi',
                'diverifikasi_oleh',
                'diverifikasi_at',
                'catatan_verifikasi',
                'denda_rusak',
                'denda_total',
                'denda_asli',
                'kondisi_kembali',
                'catatan_kondisi'
            ]);
        });
    }
};