<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('extended_from_contract_id')->nullable()->after('is_addendum');
            $table->index('extended_from_contract_id');
        });
    }

    public function down(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropIndex(['extended_from_contract_id']);
            $table->dropColumn('extended_from_contract_id');
        });
    }
};
