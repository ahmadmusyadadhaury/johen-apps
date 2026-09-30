<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->foreignId('influencer_id')->nullable()->after('pengaju_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('influencer_id');
        });
    }
};
