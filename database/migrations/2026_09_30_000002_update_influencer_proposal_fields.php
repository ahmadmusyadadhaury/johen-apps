<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('nama');
            $table->unsignedTinyInteger('rekomendasi_lama_kontrak')->nullable()->after('divisi');
            $table->date('mulai_kontrak')->nullable()->change();
            $table->date('habis_kontrak')->nullable()->change();
        });

        Schema::table('influencers', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        DB::table('influencer_pengajuans')
            ->whereNull('mulai_kontrak')
            ->update(['mulai_kontrak' => now()->toDateString()]);
        DB::table('influencer_pengajuans')
            ->whereNull('habis_kontrak')
            ->update(['habis_kontrak' => now()->toDateString()]);

        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn('divisi');
        });

        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['divisi', 'rekomendasi_lama_kontrak']);
            $table->date('mulai_kontrak')->nullable(false)->change();
            $table->date('habis_kontrak')->nullable(false)->change();
        });
    }
};
