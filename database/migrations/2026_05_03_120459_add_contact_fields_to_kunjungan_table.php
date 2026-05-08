<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            if (!Schema::hasColumn('kunjungan', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('kelas');
            }
            if (!Schema::hasColumn('kunjungan', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }
            if (!Schema::hasColumn('kunjungan', 'keperluan')) {
                $table->string('keperluan')->nullable()->after('alamat');
            }
        });
    }

    public function down()
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'alamat', 'keperluan']);
        });
    }
};