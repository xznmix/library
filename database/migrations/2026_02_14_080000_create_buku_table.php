<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            
            // Identitas Buku
            $table->string('judul');
            $table->string('pengarang')->nullable();
            $table->string('penerbit')->nullable();
            $table->year('tahun_terbit')->nullable();
            $table->string('isbn')->nullable();
            $table->string('issn')->nullable();
            $table->string('edisi')->nullable();
            $table->string('cetakan')->nullable();
            $table->integer('jumlah_halaman')->nullable();
            $table->string('bahasa')->default('Indonesia');
            
            // Klasifikasi
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_buku')->nullOnDelete();
            $table->string('sub_kategori')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->string('rak')->nullable();
            $table->string('baris')->nullable();
            
            // Fisik Buku
            $table->enum('tipe', ['fisik', 'digital'])->default('fisik');
            $table->string('format')->nullable();
            $table->string('sampul')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('berat')->nullable();
            $table->string('warna')->nullable();
            
            // Status dan Stok
            $table->integer('stok')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_dipinjam')->default(0);
            $table->integer('stok_rusak')->default(0);
            $table->integer('stok_hilang')->default(0);
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'hilang', 'dipesan'])->default('tersedia');
            
            // Statistik
            $table->integer('total_dipinjam')->default(0);
            $table->decimal('total_denda', 10, 2)->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('jumlah_ulasan')->default(0);
            $table->integer('views')->default(0);
            
            // Digital (untuk e-book)
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->string('cover_path')->nullable();
            $table->boolean('drm_enabled')->default(false);
            $table->string('drm_key')->nullable();
            $table->enum('access_level', ['public', 'member_only', 'premium'])->default('member_only');
            
            // Tanggal
            $table->date('tanggal_pengadaan')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->timestamp('terakhir_dipinjam')->nullable();
            
            // Keterangan
            $table->text('deskripsi')->nullable();
            $table->text('sinopsis')->nullable();
            $table->text('daftar_isi')->nullable();
            $table->string('kata_kunci')->nullable();
            $table->text('catatan')->nullable();
            
            // Sumber
            $table->string('sumber_id')->nullable();
            $table->string('sumber_nama')->nullable();
            $table->decimal('harga', 10, 2)->default(0);
            $table->decimal('harga_sewa', 10, 2)->default(0);
            $table->decimal('denda_per_hari', 10, 2)->default(1000);
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index('judul');
            $table->index('isbn');
            $table->index('pengarang');
            $table->index('status');
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};