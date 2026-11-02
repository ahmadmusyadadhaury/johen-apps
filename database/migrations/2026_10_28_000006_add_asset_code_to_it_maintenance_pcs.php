<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_pcs', function (Blueprint $table) {
            $table->string('asset_code')->nullable()->unique()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('it_maintenance_pcs', function (Blueprint $table) {
            $table->dropUnique(['asset_code']);
            $table->dropColumn('asset_code');
        });
    }
};
