<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->text('feedback_koordinator')->nullable()->after('feedback_atasan');
        });
    }

    public function down(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('feedback_koordinator');
        });
    }
};
