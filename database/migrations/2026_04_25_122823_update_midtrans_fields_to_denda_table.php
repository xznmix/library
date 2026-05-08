<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah tabel denda ada
        if (Schema::hasTable('denda')) {
            Schema::table('denda', function (Blueprint $table) {
                // Cek dan tambahkan kolom jika belum ada
                if (!Schema::hasColumn('denda', 'midtrans_order_id')) {
                    $table->string('midtrans_order_id')->nullable()->after('keterangan');
                }
                
                if (!Schema::hasColumn('denda', 'midtrans_token')) {
                    $table->string('midtrans_token')->nullable()->after('midtrans_order_id');
                }
                
                if (!Schema::hasColumn('denda', 'payment_status')) {
                    $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('midtrans_token');
                }
                
                if (!Schema::hasColumn('denda', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('payment_status');
                }
                
                if (!Schema::hasColumn('denda', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_method');
                }
                
                if (!Schema::hasColumn('denda', 'payment_verified_by')) {
                    $table->string('payment_verified_by')->nullable()->after('paid_at');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('denda')) {
            Schema::table('denda', function (Blueprint $table) {
                $columns = ['midtrans_order_id', 'midtrans_token', 'payment_status', 
                           'payment_method', 'paid_at', 'payment_verified_by'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('denda', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};