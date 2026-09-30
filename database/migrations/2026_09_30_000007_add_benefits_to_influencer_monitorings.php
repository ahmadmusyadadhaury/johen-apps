<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('influencer_monitorings', function (Blueprint $table) {
            $table->text('benefits')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('influencer_monitorings', function (Blueprint $table) {
            $table->dropColumn('benefits');
        });
    }
};
