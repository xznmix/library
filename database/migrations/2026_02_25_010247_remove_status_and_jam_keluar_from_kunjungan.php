<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            if (Schema::hasColumn('kunjungan', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('kunjungan', 'jam_keluar')) {
                $table->dropColumn('jam_keluar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'selesai'])->default('aktif')->after('jam_masuk');
            $table->time('jam_keluar')->nullable()->after('status');
        });
    }
};