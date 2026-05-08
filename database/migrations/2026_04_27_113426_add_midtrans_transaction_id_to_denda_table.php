<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('denda')) {
            Schema::table('denda', function (Blueprint $table) {
                // Tambahkan midtrans_transaction_id jika belum ada
                if (!Schema::hasColumn('denda', 'midtrans_transaction_id')) {
                    $table->string('midtrans_transaction_id')->nullable()->after('midtrans_token');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('denda')) {
            Schema::table('denda', function (Blueprint $table) {
                if (Schema::hasColumn('denda', 'midtrans_transaction_id')) {
                    $table->dropColumn('midtrans_transaction_id');
                }
            });
        }
    }
};