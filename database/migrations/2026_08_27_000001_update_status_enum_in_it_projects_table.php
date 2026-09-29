<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sintaks MODIFY COLUMN hanya tersedia di MySQL/MariaDB. Pada driver
        // lain (mis. sqlite saat test) lewati saja perubahan tipe kolomnya;
        // transformasi data di bawah tetap dijalankan.
        $isMySql = DB::connection()->getDriverName() === 'mysql';

        if ($isMySql) {
            // Temporarily expand enum to include all values
            DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('aktif','menunggu','proses','selesai') DEFAULT 'menunggu'");
        }

        // Migrate existing data
        DB::table('it_projects')->where('status', 'aktif')->update(['status' => 'proses']);

        if ($isMySql) {
            // Set final enum
            DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('menunggu','proses','selesai') DEFAULT 'menunggu'");
        }
    }

    public function down(): void
    {
        DB::table('it_projects')->where('status', 'proses')->update(['status' => 'aktif']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('aktif','selesai') DEFAULT 'aktif'");
        }
    }
};
