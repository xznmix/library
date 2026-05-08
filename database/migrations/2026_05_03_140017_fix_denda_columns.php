<?php
// database/migrations/2026_05_03_000001_fix_denda_columns.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Perbaiki tabel peminjaman - tambah kolom denda_total jika belum ada
        Schema::table('peminjaman', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman', 'denda_total')) {
                $table->decimal('denda_total', 12, 0)->default(0)->after('status_pinjam');
            }
            
            // Tambah kolom extra_attributes untuk menyimpan detail denda
            if (!Schema::hasColumn('peminjaman', 'extra_attributes')) {
                $table->json('extra_attributes')->nullable()->after('denda_total');
            }
        });
        
        // Perbaiki tabel denda
        Schema::table('denda', function (Blueprint $table) {
            if (!Schema::hasColumn('denda', 'denda_terlambat')) {
                $table->decimal('denda_terlambat', 12, 0)->default(0)->after('jumlah_denda');
            }
            if (!Schema::hasColumn('denda', 'denda_kerusakan')) {
                $table->decimal('denda_kerusakan', 12, 0)->default(0)->after('denda_terlambat');
            }
            if (!Schema::hasColumn('denda', 'hari_terlambat')) {
                $table->integer('hari_terlambat')->default(0)->after('denda_kerusakan');
            }
            if (!Schema::hasColumn('denda', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('paid_at');
            }
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['extra_attributes']);
        });
        
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn(['denda_terlambat', 'denda_kerusakan', 'hari_terlambat', 'confirmed_by']);
        });
    }
};