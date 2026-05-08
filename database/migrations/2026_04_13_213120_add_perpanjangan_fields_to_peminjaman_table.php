<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->boolean('is_perpanjangan')->default(false)->after('status_pinjam');
            $table->unsignedBigInteger('parent_peminjaman_id')->nullable()->after('is_perpanjangan');
            $table->foreign('parent_peminjaman_id')->references('id')->on('peminjaman')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['parent_peminjaman_id']);
            $table->dropColumn(['is_perpanjangan', 'parent_peminjaman_id']);
        });
    }
};