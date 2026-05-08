<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('buku_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')
                ->constrained('buku')
                ->cascadeOnDelete();
            $table->string('aktivitas');
            $table->integer('jumlah')->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buku_logs');
    }
};