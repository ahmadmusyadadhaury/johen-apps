<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_punches', function (Blueprint $table) {
            $table->id();
            $table->string('machine_user_id', 20)->index();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('punch_at');
            $table->string('method', 20)->default('finger');
            $table->string('machine_serial', 50)->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->unique(['machine_user_id', 'punch_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_punches');
    }
};
