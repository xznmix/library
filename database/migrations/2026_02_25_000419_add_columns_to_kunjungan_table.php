<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            // Cek apakah kolom sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('kunjungan', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('kelas');
            }
            
            if (!Schema::hasColumn('kunjungan', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }
            
            if (!Schema::hasColumn('kunjungan', 'keperluan')) {
                $table->string('keperluan')->nullable()->after('alamat');
            }
            
            if (!Schema::hasColumn('kunjungan', 'petugas_id')) {
                $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $columns = ['no_hp', 'alamat', 'keperluan', 'petugas_id'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('kunjungan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};