<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poin_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('poin')->default(0);
            $table->string('keterangan')->nullable();
            $table->enum('jenis', ['tambah', 'kurang'])->default('tambah');
            $table->string('referensi')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poin_anggota');
    }
};