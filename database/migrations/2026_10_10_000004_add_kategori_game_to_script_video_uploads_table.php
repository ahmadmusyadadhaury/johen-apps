<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->string('kategori_game')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('script_video_uploads', function (Blueprint $table) {
            $table->dropColumn('kategori_game');
        });
    }
};