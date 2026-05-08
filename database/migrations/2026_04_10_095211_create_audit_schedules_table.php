<?php
// database/migrations/2026_01_15_000001_create_audit_schedules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_id')->constrained('buku')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->date('scheduled_date');
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['status', 'scheduled_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_schedules');
    }
};