<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Data Identitas
            $table->string('jenis')->nullable()->after('role');
            $table->string('kelas')->nullable()->after('jenis');
            $table->string('jurusan')->nullable()->after('kelas');
            
            // Data Keanggotaan
            $table->string('no_anggota')->nullable()->unique()->after('id');
            $table->enum('status_anggota', ['pending', 'active', 'inactive', 'rejected'])
                  ->default('pending')->after('status');
            $table->date('tanggal_daftar')->nullable()->after('status_anggota');
            $table->date('masa_berlaku')->nullable()->after('tanggal_daftar');
            $table->text('catatan_penolakan')->nullable()->after('masa_berlaku');
            $table->timestamp('approved_at')->nullable()->after('catatan_penolakan');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
            
            // Upload Files
            $table->string('foto_ktp')->nullable()->after('address');
            $table->string('foto_kartu')->nullable()->after('foto_ktp');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'jenis',
                'kelas',
                'jurusan',
                'no_anggota',
                'status_anggota',
                'tanggal_daftar',
                'masa_berlaku',
                'catatan_penolakan',
                'approved_at',
                'approved_by',
                'foto_ktp',
                'foto_kartu'
            ]);
        });
    }
};