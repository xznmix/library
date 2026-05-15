<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denda', function (Blueprint $table) {
            if (!Schema::hasColumn('denda', 'id_anggota')) {
                $table->unsignedBigInteger('id_anggota')->nullable()->after('peminjaman_id');
                $table->index('id_anggota');
            }
        });

        // Isi id_anggota dari relasi peminjaman (untuk data lama)
        if (Schema::hasTable('peminjaman')) {
            DB::statement('
                UPDATE denda d
                JOIN peminjaman p ON p.id = d.peminjaman_id
                SET d.id_anggota = p.user_id
                WHERE d.id_anggota IS NULL
            ');
        }
    }

    public function down(): void
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn('id_anggota');
        });
    }
};