<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('denda', function (Blueprint $table) {
            if (!Schema::hasColumn('denda', 'tanggal_bayar')) {
                $table->timestamp('tanggal_bayar')->nullable()->after('paid_at');
            }
        });
    }

    public function down()
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn('tanggal_bayar');
        });
    }
};