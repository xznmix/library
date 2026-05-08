<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->enum('kondisi_kembali', [
                'baik',
                'rusak_ringan',
                'rusak_berat',
                'hilang'
            ])->nullable()->after('denda');
            
            $table->text('catatan_kondisi')->nullable()->after('kondisi_kembali');
            $table->decimal('denda_rusak', 10, 2)->default(0)->after('catatan_kondisi');
            $table->decimal('denda_total', 10, 2)->default(0)->after('denda_rusak');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['kondisi_kembali', 'catatan_kondisi', 'denda_rusak', 'denda_total']);
        });
    }
};