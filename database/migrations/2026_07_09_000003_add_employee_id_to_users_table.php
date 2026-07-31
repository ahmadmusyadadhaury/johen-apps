<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'employee_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        $employees = DB::table('employees')->whereNotNull('user_id')->get();
        foreach ($employees as $emp) {
            DB::table('users')->where('id', $emp->user_id)->update(['employee_id' => $emp->id]);
        }

        if (Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropUnique(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('employees', 'user_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained()->nullOnDelete();
            });
        }

        $users = DB::table('users')->whereNotNull('employee_id')->get();
        foreach ($users as $u) {
            DB::table('employees')->where('id', $u->employee_id)->update(['user_id' => $u->id]);
        }

        if (Schema::hasColumn('users', 'employee_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('employee_id');
            });
        }
    }
};
