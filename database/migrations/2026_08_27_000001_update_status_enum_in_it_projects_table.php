<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Temporarily expand enum to include all values
        DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('aktif','menunggu','proses','selesai') DEFAULT 'menunggu'");
        // Migrate existing data
        DB::table('it_projects')->where('status', 'aktif')->update(['status' => 'proses']);
        // Set final enum
        DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('menunggu','proses','selesai') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::table('it_projects')->where('status', 'proses')->update(['status' => 'aktif']);
        DB::statement("ALTER TABLE it_projects MODIFY COLUMN status ENUM('aktif','selesai') DEFAULT 'aktif'");
    }
};
