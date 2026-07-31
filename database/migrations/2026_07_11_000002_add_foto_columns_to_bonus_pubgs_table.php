<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->string('foto_bukti_stats')->nullable()->after('catatan');
            $table->string('foto_bukti_live')->nullable()->after('foto_bukti_stats');
        });
    }

    public function down(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti_stats', 'foto_bukti_live']);
        });
    }
};
