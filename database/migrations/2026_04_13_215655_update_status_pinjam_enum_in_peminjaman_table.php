<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // MySQL: Ubah enum dengan cara drop dulu lalu buat ulang
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_pinjam ENUM('dipinjam', 'terlambat', 'dikembalikan', 'diperpanjang') NOT NULL DEFAULT 'dipinjam'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_pinjam ENUM('dipinjam', 'terlambat', 'dikembalikan') NOT NULL DEFAULT 'dipinjam'");
    }
};