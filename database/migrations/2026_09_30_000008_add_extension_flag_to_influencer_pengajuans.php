<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->boolean('is_perpanjangan')->default(false)->after('rekomendasi_lama_kontrak');
        });
    }

    public function down(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->dropColumn('is_perpanjangan');
        });
    }
};
