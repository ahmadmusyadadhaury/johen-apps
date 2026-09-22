<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('recurring_prev_day')->nullable()->after('recurring_day');
            $table->date('recurring_start_date')->nullable()->after('recurring_prev_day');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['recurring_prev_day', 'recurring_start_date']);
        });
    }
};