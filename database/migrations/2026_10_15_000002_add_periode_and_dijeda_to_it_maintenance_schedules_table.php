<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('it_maintenance_schedules', 'periode')) {
            Schema::table('it_maintenance_schedules', function (Blueprint $table) {
                $table->integer('periode')->default(1)->after('pc_id');
            });
        }

        // Kolom status dipakai untuk nilai 'dijeda', tetapi migration
        // sebelumnya hanya mendefinisikan ('antrean','diproses','selesai').
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('antrean','diproses','dijeda','selesai') NOT NULL DEFAULT 'antrean'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('antrean','diproses','selesai') NOT NULL DEFAULT 'antrean'");
        }

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
};
