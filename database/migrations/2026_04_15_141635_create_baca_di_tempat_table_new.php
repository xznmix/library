<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('baca_di_tempat')) {
            Schema::create('baca_di_tempat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
                $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('barcode_buku')->nullable();
                $table->string('no_anggota')->nullable();
                $table->dateTime('waktu_mulai');
                $table->dateTime('waktu_selesai')->nullable();
                $table->integer('durasi_menit')->nullable();
                $table->integer('poin_didapat')->default(0);
                $table->string('lokasi')->default('Ruang Baca Umum');
                $table->enum('status', ['sedang_baca', 'selesai'])->default('sedang_baca');
                $table->text('catatan')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index('waktu_mulai');
                $table->index('no_anggota');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('baca_di_tempat');
    }
};