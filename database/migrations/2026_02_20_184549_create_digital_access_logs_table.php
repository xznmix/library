<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digital_access_logs', function (Blueprint $table) {
            $table->id();
            
            // Perbaiki foreign key dengan tanda kurung yang benar
            $table->foreignId('peminjaman_digital_id')
                  ->constrained('peminjaman_digital')
                  ->cascadeOnDelete();  // <<< HARUS PAKAI TANDA KURUNG ()
                  
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();  // <<< TAMBAHKAN ()
                  
            $table->foreignId('buku_id')
                  ->constrained('buku')
                  ->cascadeOnDelete();  // <<< TAMBAHKAN ()
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('lokasi_perkiraan')->nullable();
            $table->enum('aksi', ['baca', 'download', 'cetak', 'salin', 'pinjam', 'kembali', 'blokir'])->default('baca');
            $table->enum('status', ['berhasil', 'gagal', 'diblokir'])->default('berhasil');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['user_id', 'created_at']);
            $table->index(['buku_id', 'created_at']);
            $table->index(['peminjaman_digital_id', 'created_at']);
            $table->index('status');
            $table->index('aksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digital_access_logs');
    }
};