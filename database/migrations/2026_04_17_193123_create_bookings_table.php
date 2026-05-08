<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_booking', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->date('tanggal_ambil');
            $table->dateTime('batas_ambil');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'diambil', 'hangus'])->default('menunggu');
            $table->text('catatan_penolakan')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('diproses_menjadi_peminjaman_id')->nullable()->constrained('peminjaman')->onDelete('set null');
            $table->timestamps();
            
            $table->index('status');
            $table->index('user_id');
            $table->index('buku_id');
            $table->index('batas_ambil');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};