<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_plan_reports', function (Blueprint $table) {
            $table->string('foto_bukti_stats')->nullable()->after('action_plan');
            $table->string('foto_bukti_live')->nullable()->after('foto_bukti_stats');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_plan_reports', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti_stats', 'foto_bukti_live']);
        });
    }
};
