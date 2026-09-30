<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('influencer_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('influencer_id')->constrained()->cascadeOnDelete();
            $table->date('period_month');
            $table->unsignedBigInteger('followers')->default(0);
            $table->unsignedBigInteger('viewers_last_month')->default(0);
            $table->decimal('duration_hours', 8, 2)->default(0);
            $table->decimal('target_duration_hours', 8, 2)->default(130);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['influencer_id', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('influencer_monitorings');
    }
};
