<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('running_rate_host_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('running_rate_periods')->cascadeOnDelete();
            $table->foreignId('host_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('target', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['period_id', 'host_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('running_rate_host_targets');
    }
};
