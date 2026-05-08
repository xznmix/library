<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ulasan_buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete(); // Perbaiki: 'buku' bukan 'bukus'
            $table->integer('rating')->unsigned()->min(1)->max(5);
            $table->text('ulasan')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi rating per user per buku
            $table->unique(['user_id', 'buku_id']);
            
            // Index untuk optimasi query
            $table->index('buku_id');
            $table->index('rating');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ulasan_buku');
    }
};