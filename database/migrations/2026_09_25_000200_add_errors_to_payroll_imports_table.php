<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_imports', function (Blueprint $table) {
            $table->json('errors')->nullable()->after('total_payroll');
            $table->unsignedInteger('invalid_rows')->default(0)->after('errors');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_imports', function (Blueprint $table) {
            $table->dropColumn(['errors', 'invalid_rows']);
        });
    }
};