<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('buku_penulis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete();
            $table->foreignId('penulis_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
            
            // Unique constraint
            $table->unique(['buku_id', 'penulis_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('buku_penulis');
    }
};