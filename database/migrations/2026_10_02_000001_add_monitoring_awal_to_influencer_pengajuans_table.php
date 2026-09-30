<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->date('monitoring_month')->nullable()->after('keterangan');
            $table->unsignedBigInteger('monitoring_followers')->nullable()->after('monitoring_month');
            $table->unsignedBigInteger('monitoring_viewers_last_month')->nullable()->after('monitoring_followers');
            $table->decimal('monitoring_duration_hours', 8, 2)->nullable()->after('monitoring_viewers_last_month');
            $table->decimal('monitoring_target_duration_hours', 8, 2)->nullable()->after('monitoring_duration_hours');
            $table->text('monitoring_notes')->nullable()->after('monitoring_target_duration_hours');
            $table->text('monitoring_benefits')->nullable()->after('monitoring_notes');
        });
    }

    public function down(): void
    {
        Schema::table('influencer_pengajuans', function (Blueprint $table) {
            $table->dropColumn([
                'monitoring_month',
                'monitoring_followers',
                'monitoring_viewers_last_month',
                'monitoring_duration_hours',
                'monitoring_target_duration_hours',
                'monitoring_notes',
                'monitoring_benefits',
            ]);
        });
    }
};
