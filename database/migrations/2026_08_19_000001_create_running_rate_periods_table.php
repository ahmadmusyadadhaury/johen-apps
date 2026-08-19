<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('running_rate_periods', function (Blueprint $table) {
            $table->id();
            $table->string('divisi');
            $table->string('nama');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['divisi', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('running_rate_periods');
    }
};
