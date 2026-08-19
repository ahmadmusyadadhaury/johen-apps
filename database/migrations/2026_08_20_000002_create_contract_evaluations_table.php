<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('employee_contracts')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('kinerja')->nullable();
            $table->tinyInteger('disiplin')->nullable();
            $table->tinyInteger('kerjasama')->nullable();
            $table->tinyInteger('kepatuhan')->nullable();
            $table->tinyInteger('keterampilan')->nullable();
            $table->text('catatan')->nullable();
            $table->string('rekomendasi', 50)->nullable();
            $table->timestamps();
            $table->unique('contract_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_evaluations');
    }
};