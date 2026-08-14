<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->string('divisi')->nullable()->after('sesi');
        });

        // Backfill memakai sintaks UPDATE ... JOIN yang khusus MySQL.
        // Di driver lain (mis. SQLite untuk test) kolom tetap dibuat dan
        // backfill data diproses oleh aplikasi, bukan oleh migration.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE bonus_pubgs bp
                LEFT JOIN employees e ON e.id = bp.employee_id
                LEFT JOIN users u ON u.employee_id = e.id
                SET bp.divisi = CASE
                    WHEN u.role IN ('staff_host_pubg', 'koordinator_pubg') THEN 'PUBG'
                    WHEN u.role IN ('staff_host_ff', 'koordinator_ff') THEN 'Free Fire'
                    WHEN u.role IN ('staff_host_mlbb', 'koordinator_mlbb') THEN 'MLBB'
                    WHEN u.role IN ('staff_host_efootball', 'koordinator_efootball') THEN 'E-football'
                    WHEN u.role IN ('staff_host_valorant', 'koordinator_valorant') THEN 'Valorant'
                    WHEN u.role IN ('staff_host_roblox', 'koordinator_roblox') THEN 'Roblox'
                    WHEN u.role IN ('staff_host_monkey_pubg', 'koordinator_monkey_pubg') THEN 'Monkey PUBG'
                    WHEN u.role IN ('staff_admin', 'koordinator_admin') THEN 'Admin'
                    ELSE NULL
                END
            ");
        }
    }

    public function down(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->dropColumn('divisi');
        });
    }
};
