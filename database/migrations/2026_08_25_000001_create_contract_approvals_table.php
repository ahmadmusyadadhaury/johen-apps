<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('employee_contracts')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('decision', ['disetujui', 'tidak_disetujui']);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['contract_id', 'approver_id'], 'contract_approvals_contract_approver_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_approvals');
    }
};
