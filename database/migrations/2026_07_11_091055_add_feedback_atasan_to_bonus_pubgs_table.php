<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->text('feedback_atasan')->nullable()->after('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('bonus_pubgs', function (Blueprint $table) {
            $table->dropColumn('feedback_atasan');
        });
    }
};
