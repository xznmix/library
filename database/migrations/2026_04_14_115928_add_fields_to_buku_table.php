<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('buku', function (Blueprint $table) {
            // Cek dan tambahkan kolom yang belum ada
            
            // ========== DATA EKSEMPLAR ==========
            if (!Schema::hasColumn('buku', 'barcode')) {
                $table->string('barcode')->nullable()->after('isbn');
            }
            if (!Schema::hasColumn('buku', 'rfid')) {
                $table->string('rfid')->nullable()->after('barcode');
            }
            if (!Schema::hasColumn('buku', 'sumber_jenis')) {
                $table->string('sumber_jenis')->nullable()->after('sumber_nama');
            }
            if (!Schema::hasColumn('buku', 'kode_lokasi_perpus')) {
                $table->string('kode_lokasi_perpus')->nullable()->after('sumber_jenis');
            }
            if (!Schema::hasColumn('buku', 'kode_lokasi_ruang')) {
                $table->string('kode_lokasi_ruang')->nullable()->after('kode_lokasi_perpus');
            }
            
            // ========== DATA BIBLIOGRAFIS ==========
            if (!Schema::hasColumn('buku', 'sub_judul')) {
                $table->string('sub_judul')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('buku', 'pernyataan_tanggungjawab')) {
                $table->text('pernyataan_tanggungjawab')->nullable()->after('sub_judul');
            }
            if (!Schema::hasColumn('buku', 'pengarang_badan')) {
                $table->string('pengarang_badan')->nullable()->after('pengarang');
            }
            if (!Schema::hasColumn('buku', 'pengarang_tambahan')) {
                $table->string('pengarang_tambahan')->nullable()->after('pengarang_badan');
            }
            if (!Schema::hasColumn('buku', 'kota_terbit')) {
                $table->string('kota_terbit')->nullable()->after('penerbit');
            }
            // ISSN sudah ada, jangan tambahkan lagi
            if (!Schema::hasColumn('buku', 'no_ddc')) {
                $table->string('no_ddc')->nullable()->after('issn');
            }
            if (!Schema::hasColumn('buku', 'nomor_panggil_katalog')) {
                $table->string('nomor_panggil_katalog')->nullable()->after('no_ddc');
            }
            if (!Schema::hasColumn('buku', 'nomor_panggil')) {
                $table->string('nomor_panggil')->nullable()->after('nomor_panggil_katalog');
            }
            if (!Schema::hasColumn('buku', 'kata_kunci')) {
                $table->string('kata_kunci')->nullable()->after('deskripsi');
            }
            
            // ========== DATA TERBITAN BERKALA ==========
            if (!Schema::hasColumn('buku', 'edisi_serial')) {
                $table->string('edisi_serial')->nullable();
            }
            if (!Schema::hasColumn('buku', 'tanggal_terbit_serial')) {
                $table->date('tanggal_terbit_serial')->nullable();
            }
            if (!Schema::hasColumn('buku', 'bahan_sertaan')) {
                $table->string('bahan_sertaan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('buku', function (Blueprint $table) {
            $columns = [
                'barcode', 'rfid', 'sumber_jenis', 'kode_lokasi_perpus', 'kode_lokasi_ruang',
                'sub_judul', 'pernyataan_tanggungjawab', 'pengarang_badan', 'pengarang_tambahan',
                'kota_terbit', 'no_ddc', 'nomor_panggil_katalog', 'nomor_panggil',
                'kata_kunci', 'edisi_serial', 'tanggal_terbit_serial', 'bahan_sertaan'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('buku', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};