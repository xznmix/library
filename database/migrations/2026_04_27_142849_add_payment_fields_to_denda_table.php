<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToDendaTable extends Migration
{
    public function up()
    {
        Schema::table('denda', function (Blueprint $table) {
            if (!Schema::hasColumn('denda', 'kode_pembayaran')) {
                $table->string('kode_pembayaran', 50)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('denda', 'confirmed_by')) {
                $table->bigInteger('confirmed_by')->unsigned()->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('denda', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('kode_pembayaran');
            }
        });
    }

    public function down()
    {
        Schema::table('denda', function (Blueprint $table) {
            $table->dropColumn(['kode_pembayaran', 'confirmed_by', 'qr_code_path']);
        });
    }
}