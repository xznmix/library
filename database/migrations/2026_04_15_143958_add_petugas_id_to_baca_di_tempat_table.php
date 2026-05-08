<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('baca_di_tempat', function (Blueprint $table) {
            if (!Schema::hasColumn('baca_di_tempat', 'petugas_id')) {
                $table->unsignedBigInteger('petugas_id')->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('baca_di_tempat', 'updated_by')) {
                $table->string('updated_by')->nullable()->after('petugas_id');
            }
        });
    }

    public function down()
    {
        Schema::table('baca_di_tempat', function (Blueprint $table) {
            $table->dropColumn(['petugas_id', 'updated_by']);
        });
    }
};