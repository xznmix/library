<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('jenis_anggota')->nullable(); // siswa, guru, pegawai, umum
            $table->date('tanggal_daftar')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->enum('status_keanggotaan', ['active', 'nonactive', 'pending'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('anggota');
    }
};