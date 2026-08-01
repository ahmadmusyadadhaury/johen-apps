<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_projects', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('deadline');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_projects');
    }
};
