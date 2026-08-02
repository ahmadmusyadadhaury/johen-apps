<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->string('foto_sebelum')->nullable()->after('catatan');
            $table->string('foto_sesudah')->nullable()->after('foto_sebelum');
        });
    }

    public function down(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn(['foto_sebelum', 'foto_sesudah']);
        });
    }
};
