<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_evaluations', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropUnique('contract_evaluations_contract_id_unique');
            $table->unique(['contract_id', 'evaluator_id'], 'contract_evaluations_contract_evaluator_unique');
            $table->foreign('contract_id')->references('id')->on('employee_contracts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contract_evaluations', function (Blueprint $table) {
            $table->dropForeign(['contract_id']);
            $table->dropUnique('contract_evaluations_contract_evaluator_unique');
            $table->unique('contract_id');
            $table->foreign('contract_id')->references('id')->on('employee_contracts')->cascadeOnDelete();
        });
    }
};