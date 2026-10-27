<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('jenis', 20)->default('pelatihan');
            $table->text('deskripsi')->nullable();
            $table->dateTime('mulai_pada');
            $table->dateTime('selesai_pada');
            $table->string('lokasi')->nullable();
            $table->string('tautan')->nullable();
            $table->unsignedInteger('kuota')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['is_published', 'mulai_pada']);
        });

        Schema::create('training_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('training_sessions')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('status', 20)->default('registered');
            $table->dateTime('terdaftar_pada');
            $table->dateTime('dihadiri_pada')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['training_session_id', 'employee_id']);
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_registrations');
        Schema::dropIfExists('training_sessions');
    }
};
