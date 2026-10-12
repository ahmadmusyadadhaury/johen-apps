<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->boolean('konten_dibuat')->default(false)->after('kategori_game');
        });
    }

    public function down(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->dropColumn('konten_dibuat');
        });
    }
};