<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->string('kontrak_file_path')->nullable()->after('biaya');
        });
    }

    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn('kontrak_file_path');
        });
    }
};
