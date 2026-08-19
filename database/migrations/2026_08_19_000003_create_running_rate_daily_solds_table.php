<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('running_rate_daily_solds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('running_rate_periods')->cascadeOnDelete();
            $table->foreignId('host_id')->constrained('employees')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('sold', 10, 2)->default(0);
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['period_id', 'host_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('running_rate_daily_solds');
    }
};
