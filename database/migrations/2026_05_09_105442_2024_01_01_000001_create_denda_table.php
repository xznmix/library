<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('denda')) {
            Schema::create('denda', function (Blueprint $table) {
                $table->id();
                $table->foreignId('peminjaman_id')
                    ->constrained('peminjaman')
                    ->cascadeOnDelete();
                $table->decimal('jumlah_denda', 12, 0)->default(0);
                $table->enum('status', ['pending', 'lunas', 'failed'])->default('pending');
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
                $table->enum('payment_method', ['qris', 'tunai', 'transfer'])->nullable();
                $table->decimal('denda_terlambat', 12, 0)->default(0);
                $table->decimal('denda_kerusakan', 12, 0)->default(0);
                $table->integer('hari_terlambat')->default(0);
                $table->string('midtrans_order_id')->nullable();
                $table->string('midtrans_token')->nullable();
                $table->string('midtrans_transaction_id')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('tanggal_bayar')->nullable();
                $table->string('kode_pembayaran', 50)->nullable();
                $table->string('qr_code_path')->nullable();
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->string('payment_verified_by')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
                
                $table->index('peminjaman_id');
                $table->index('status');
                $table->index('payment_status');
                $table->index('midtrans_order_id');
                $table->index('kode_pembayaran');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};