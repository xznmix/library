<?php
// database/migrations/[timestamp]_add_complete_registration_fields_to_users.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek dan tambah kolom yang belum ada
            if (!Schema::hasColumn('users', 'nisn_nik')) {
                $table->string('nisn_nik')->nullable()->unique()->after('name');
            }
            
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'pekerjaan')) {
                $table->string('pekerjaan')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('pekerjaan');
            }
            
            if (!Schema::hasColumn('users', 'jenis')) {
                $table->string('jenis')->default('umum')->after('address');
            }
            
            if (!Schema::hasColumn('users', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('jenis');
            }
            
            if (!Schema::hasColumn('users', 'verification_token')) {
                $table->string('verification_token')->nullable()->after('submitted_at');
            }
            
            if (!Schema::hasColumn('users', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('verification_token');
            }
            
            if (!Schema::hasColumn('users', 'processed_by')) {
                $table->foreignId('processed_by')->nullable()->constrained('users')->after('processed_at');
            }
            
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('processed_by');
            }
            
            if (!Schema::hasColumn('users', 'status_anggota')) {
                $table->enum('status_anggota', ['pending', 'active', 'inactive', 'rejected'])
                      ->default('pending')->after('rejection_reason');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'nisn_nik', 'phone', 'pekerjaan', 'address', 'jenis',
                'submitted_at', 'verification_token', 'processed_at',
                'processed_by', 'rejection_reason', 'status_anggota'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};