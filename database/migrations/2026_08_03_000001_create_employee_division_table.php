<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_division', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'division_id']);
        });

        // Backfill pivot from the legacy single division_id column
        $employees = Employee::whereNotNull('division_id')->get(['id', 'division_id']);
        foreach ($employees as $emp) {
            DB::table('employee_division')->insert([
                'employee_id' => $emp->id,
                'division_id' => $emp->division_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop the legacy column; the pivot is now the source of truth
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('division_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('status')->constrained()->nullOnDelete();
        });

        // Restore a single legacy division per employee (first pivot row)
        $rows = DB::table('employee_division')
            ->orderBy('id')
            ->get(['employee_id', 'division_id']);
        foreach ($rows as $row) {
            DB::table('employees')
                ->where('id', $row->employee_id)
                ->whereNull('division_id')
                ->update(['division_id' => $row->division_id]);
        }

        Schema::dropIfExists('employee_division');
    }
};
