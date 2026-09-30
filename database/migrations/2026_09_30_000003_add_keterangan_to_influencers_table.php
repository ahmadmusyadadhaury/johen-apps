<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('divisi');
        });
    }

    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
