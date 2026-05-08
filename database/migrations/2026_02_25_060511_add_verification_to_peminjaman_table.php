<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Status verifikasi denda
            $table->enum('status_verifikasi', [
                'pending',      // Menunggu verifikasi kepala pustaka
                'disetujui',    // Denda valid
                'ditolak'       // Denda dicurigai
            ])->default('pending')->after('denda_total');
            
            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('status_verifikasi');
                
            $table->timestamp('diverifikasi_at')
                ->nullable()
                ->after('diverifikasi_oleh');
                
            $table->text('catatan_verifikasi')
                ->nullable()
                ->after('diverifikasi_at');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi',
                'diverifikasi_oleh',
                'diverifikasi_at',
                'catatan_verifikasi'
            ]);
        });
    }
};