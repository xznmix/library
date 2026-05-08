<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixStatusVerifikasiEnumInPeminjaman extends Migration
{
    public function up()
    {
        // Ubah enum menjadi yang lebih sesuai
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_verifikasi ENUM('pending', 'disetujui', 'ditolak', 'selesai') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE peminjaman MODIFY COLUMN status_verifikasi ENUM('pending', 'disetujui', 'ditolak') DEFAULT 'pending'");
    }
}