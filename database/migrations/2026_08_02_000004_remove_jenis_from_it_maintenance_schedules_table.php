<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->string('jenis')->nullable()->after('pc_id');
        });
    }
};
