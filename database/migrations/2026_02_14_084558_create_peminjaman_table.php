<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Tracking unik per eksemplar
            $table->string('kode_eksemplar')->nullable()->unique();
            
            // Tanggal
            $table->date('tanggal_pinjam');
            $table->date('tgl_jatuh_tempo');
            $table->date('tanggal_pengembalian')->nullable();
            
            // Status (cukup satu kolom)
            $table->enum('status_pinjam', [
                'dipinjam',      // sedang dipinjam
                'terlambat',     // melewati jatuh tempo
                'dikembalikan'   // sudah kembali
            ])->default('dipinjam');
            
            // Denda
            $table->decimal('denda', 10, 2)->default(0);
            
            // Keterangan tambahan
            $table->text('keterangan')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index('kode_eksemplar');
            $table->index('tanggal_pinjam');
            $table->index('tgl_jatuh_tempo');
            $table->index('status_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};