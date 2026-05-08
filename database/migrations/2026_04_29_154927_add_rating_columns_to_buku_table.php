<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('buku', function (Blueprint $table) {
            if (!Schema::hasColumn('buku', 'rating')) {
                $table->decimal('rating', 2, 1)->default(0)->after('stok');
            }
            if (!Schema::hasColumn('buku', 'rating_count')) {
                $table->integer('rating_count')->default(0)->after('rating');
            }
        });
    }

    public function down()
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_count']);
        });
    }
};