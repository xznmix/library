<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            // Tambah field jenis_koleksi setelah tipe
            $table->enum('jenis_koleksi', ['ebook', 'soal', 'modul', 'dokumen'])
                  ->default('ebook')
                  ->after('tipe')
                  ->comment('Jenis koleksi digital');
                  
            // Tambah field untuk menandai bisa di-download tanpa pinjam
            $table->boolean('bisa_download')->default(false)->after('jenis_koleksi')
                  ->comment('Apakah bisa di-download tanpa pinjam');
        });
    }

    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['jenis_koleksi', 'bisa_download']);
        });
    }
};