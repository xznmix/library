<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favorit_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Unique constraint agar user tidak bisa favorit buku yang sama dua kali
            $table->unique(['user_id', 'buku_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorit_buku');
    }
};