<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pc_id')->constrained('it_maintenance_pcs')->cascadeOnDelete();
            $table->string('jenis'); // Bersihin PC, Repasta, dll
            $table->date('jadwal'); // Tanggal jadwal maintenance
            $table->enum('status', ['belum', 'selesai'])->default('belum');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['pc_id', 'jadwal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_maintenance_schedules');
    }
};
