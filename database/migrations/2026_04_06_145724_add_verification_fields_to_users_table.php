<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom sudah ada di database, skip migration ini
        // Migration ini sudah dijalankan sebelumnya
    }

    public function down(): void
    {
        // Tidak perlu rollback
    }
};