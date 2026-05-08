<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete;
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete;
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->datetime('tanggal_pinjam');
            $table->datetime('tanggal_expired');
            $table->datetime('tanggal_dikembalikan')->nullable();
            
            $table->string('token_akses', 100)->unique();
            $table->enum('status', [
                'aktif',        // Sedang dipinjam
                'expired',       // Masa pinjam habis
                'dikembalikan',  // Dikembalikan lebih awal
                'diblokir'       // Diblokir karena pelanggaran
            ])->default('aktif');
            
            // Tracking untuk keamanan
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('jumlah_akses')->default(0);
            $table->timestamp('terakhir_akses')->nullable();
            
            // Metadata
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index('token_akses');
            $table->index('status');
            $table->index('tanggal_expired');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_digital');
    }
};