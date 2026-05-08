<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('buku', function (Blueprint $table) {
            if (!Schema::hasColumn('buku', 'stok_rusak')) {
                $table->integer('stok_rusak')->default(0)->after('stok_tersedia');
            }
            if (!Schema::hasColumn('buku', 'stok_hilang')) {
                $table->integer('stok_hilang')->default(0)->after('stok_rusak');
            }
            if (!Schema::hasColumn('buku', 'harga')) {
                $table->decimal('harga', 10, 2)->nullable()->after('stok_hilang');
            }
            if (!Schema::hasColumn('buku', 'last_opname_at')) {
                $table->timestamp('last_opname_at')->nullable()->after('harga');
            }
            if (!Schema::hasColumn('buku', 'last_opname_by')) {
                $table->foreignId('last_opname_by')->nullable()->constrained('users')->after('last_opname_at');
            }
        });
    }

    public function down()
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropColumn([
                'stok_rusak',
                'stok_hilang',
                'harga',
                'last_opname_at',
                'last_opname_by'
            ]);
        });
    }
};