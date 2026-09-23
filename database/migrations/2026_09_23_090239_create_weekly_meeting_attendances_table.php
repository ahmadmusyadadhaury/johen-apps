<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weekly_meeting_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_meeting_id')->constrained('weekly_meetings')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamp('attended_at');
            $table->string('method')->default('qr_scan'); // qr_scan, manual
            $table->timestamps();

            $table->unique(['weekly_meeting_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_meeting_attendances');
    }
};