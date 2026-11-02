<?php

use App\Services\OfficeLeadershipStructureService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(OfficeLeadershipStructureService::class)->sync();
    }

    public function down(): void
    {
        // Node pimpinan mungkin sudah dipakai oleh data evaluasi/fitur lain.
        // Rollback tidak menghapus data organisasi secara destruktif.
    }
};
