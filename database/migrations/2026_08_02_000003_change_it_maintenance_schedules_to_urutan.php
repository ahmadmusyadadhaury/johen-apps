<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->after('jenis');
        });

        // Backfill & ALTER berikut memakai sintaks yang khusus MySQL.
        // Di driver lain (mis. SQLite untuk test) kolom tetap dibuat dan
        // pemeliharaan data di proses oleh aplikasi, bukan oleh migration.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                UPDATE it_maintenance_schedules s
                JOIN (
                    SELECT id, ROW_NUMBER() OVER (PARTITION BY pc_id ORDER BY id) AS rn
                    FROM it_maintenance_schedules
                ) t ON s.id = t.id
                SET s.urutan = t.rn
            ");

            Schema::table('it_maintenance_schedules', function (Blueprint $table) {
                $table->integer('urutan')->nullable(false)->change();
            });

            DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('belum','antrean','diproses','selesai') NOT NULL DEFAULT 'antrean'");
            DB::table('it_maintenance_schedules')->where('status', 'belum')->update(['status' => 'antrean']);
            DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('antrean','diproses','selesai') NOT NULL DEFAULT 'antrean'");
        }

        // Index gabungan pc_id+jadwal harus dilepas lebih dulu agar drop
        // kolom jadwal berjalan di semua driver (SQLite menolak drop column
        // yang masih menjadi bagian dari index).
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropIndex(['pc_id', 'jadwal']);
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('jadwal');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('antrean','diproses','selesai','belum') NOT NULL DEFAULT 'antrean'");
        DB::table('it_maintenance_schedules')->where('status', 'antrean')->update(['status' => 'belum']);
        DB::statement("ALTER TABLE it_maintenance_schedules MODIFY status ENUM('belum','selesai') NOT NULL DEFAULT 'belum'");

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->date('jadwal')->nullable()->after('jenis');
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
