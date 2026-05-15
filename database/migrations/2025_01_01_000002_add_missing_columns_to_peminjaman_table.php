<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'denda_total')) {
                $table->decimal('denda_total', 12, 0)->default(0)->after('status_pinjam');
            }
            if (!Schema::hasColumn('peminjaman', 'kondisi_kembali')) {
                $table->enum('kondisi_kembali', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])
                    ->nullable()->after('denda_total');
            }
            if (!Schema::hasColumn('peminjaman', 'catatan_kondisi')) {
                $table->text('catatan_kondisi')->nullable()->after('kondisi_kembali');
            }
            if (!Schema::hasColumn('peminjaman', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('petugas_id');
            }
            if (!Schema::hasColumn('peminjaman', 'status_verifikasi')) {
                $table->string('status_verifikasi')->default('pending')->after('updated_by');
            }
            if (!Schema::hasColumn('peminjaman', 'is_perpanjangan')) {
                $table->boolean('is_perpanjangan')->default(false)->after('status_verifikasi');
            }
            if (!Schema::hasColumn('peminjaman', 'parent_peminjaman_id')) {
                $table->unsignedBigInteger('parent_peminjaman_id')->nullable()->after('is_perpanjangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'denda_total',
                'kondisi_kembali',
                'catatan_kondisi',
                'updated_by',
                'status_verifikasi',
                'is_perpanjangan',
                'parent_peminjaman_id'
            ]);
        });
    }
};