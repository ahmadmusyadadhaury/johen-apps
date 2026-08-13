<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_shift_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('jam_kerja')->nullable();
            $table->time('jam_masuk')->nullable();
            $table->date('effective_date');
            $table->timestamps();

            $table->index(['employee_id', 'effective_date']);
        });

        $this->backfillBaseline();
    }

    private function backfillBaseline(): void
    {
        $employees = DB::table('employees')
            ->select(['id', 'jam_kerja', 'jam_masuk', 'tanggal_masuk'])
            ->get();

        foreach ($employees as $employee) {
            if (! $employee->jam_kerja && ! $employee->jam_masuk) {
                continue;
            }

            $earliest = DB::table('attendances')
                ->where('employee_id', $employee->id)
                ->min('date');

            $effective = $earliest
                ?: $employee->tanggal_masuk
                ?: now()->toDateString();

            DB::table('employee_shift_histories')->insert([
                'employee_id' => $employee->id,
                'jam_kerja' => $employee->jam_kerja,
                'jam_masuk' => $employee->jam_masuk,
                'effective_date' => $effective,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_shift_histories');
    }
};
