<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('buku', function (Blueprint $table) {
            // Field baru untuk kategori koleksi
            if (!Schema::hasColumn('buku', 'kategori_koleksi')) {
                $table->enum('kategori_koleksi', [
                    'buku_paket', 
                    'fisik', 
                    'referensi', 
                    'non_fiksi', 
                    'umum', 
                    'paket'
                ])->nullable()->after('kategori_id');
            }
            
            // Field untuk lokasi
            if (!Schema::hasColumn('buku', 'lokasi')) {
                $table->string('lokasi')->default('Ruang Baca Umum Perpustakaan Tambang Ilmu')->after('rak');
            }
            
            // Field untuk ketersediaan (akan diisi otomatis)
            if (!Schema::hasColumn('buku', 'ketersediaan')) {
                $table->enum('ketersediaan', ['tersedia', 'dipinjam', 'rusak', 'hilang'])->default('tersedia')->after('status');
            }
            
            // Field untuk jumlah eksemplar (stok)
            if (!Schema::hasColumn('buku', 'jumlah_eksemplar')) {
                $table->integer('jumlah_eksemplar')->default(0)->after('stok');
            }
            
            // Field untuk tanggal pengadaan
            if (!Schema::hasColumn('buku', 'tanggal_pengadaan')) {
                $table->date('tanggal_pengadaan')->nullable()->after('tanggal_terbit');
            }
        });
    }

    public function down()
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['kategori_koleksi', 'lokasi', 'ketersediaan', 'jumlah_eksemplar']);
        });
    }
};