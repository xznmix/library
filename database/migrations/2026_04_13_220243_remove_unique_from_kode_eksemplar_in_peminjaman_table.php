<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropUnique('peminjaman_kode_eksemplar_unique');
            // atau
            // $table->dropUnique(['kode_eksemplar']);
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->unique('kode_eksemplar');
        });
    }
};