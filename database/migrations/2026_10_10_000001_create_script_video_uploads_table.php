<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('script_video_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('bulan');
            $table->integer('tahun');
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();

            $table->index(['user_id', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('script_video_uploads');
    }
};