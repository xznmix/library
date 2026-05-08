<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToDendaTable extends Migration
{
    public function up()
    {
        Schema::table('denda', function (Blueprint $table) {
            // Cek apakah kolom status sudah ada
            if (!Schema::hasColumn('denda', 'status')) {
                $table->enum('status', ['pending', 'lunas', 'failed'])->default('pending')->after('jumlah_denda');
            }
            
            // Cek apakah kolom payment_status sudah ada
            if (!Schema::hasColumn('denda', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('status');
            }
            
            // Cek apakah kolom payment_method sudah ada
            if (!Schema::hasColumn('denda', 'payment_method')) {
                $table->enum('payment_method', ['qris', 'tunai'])->nullable()->after('payment_status');
            }
            
            // Cek apakah kolom paid_at sudah ada
            if (!Schema::hasColumn('denda', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }
        });
    }

    public function down()
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_status', 'payment_method', 'paid_at']);
        });
    }
}