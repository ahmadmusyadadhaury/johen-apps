<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_projects', function (Blueprint $table) {
            $table->text('feedback_atasan')->nullable()->after('created_by');
        });

        Schema::table('it_tickets', function (Blueprint $table) {
            $table->text('feedback_atasan')->nullable()->after('durasi_detik');
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->text('feedback_atasan')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('it_projects', function (Blueprint $table) {
            $table->dropColumn('feedback_atasan');
        });

        Schema::table('it_tickets', function (Blueprint $table) {
            $table->dropColumn('feedback_atasan');
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('feedback_atasan');
        });
    }
};
