<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->integer('total_tepat_waktu')->default(0);
            $table->integer('total_terlambat')->default(0);

        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'total_tepat_waktu',
                'total_terlambat'
            ]);

        });
    }
};
