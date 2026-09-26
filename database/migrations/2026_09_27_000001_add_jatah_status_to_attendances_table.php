<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha', 'cuti', 'jatah'])
                ->default('hadir')
                ->change();
        });

        $jatahApproved = DB::table('leave_requests as jatah')
            ->selectRaw('1')
            ->whereColumn('jatah.employee_id', 'attendances.employee_id')
            ->where('jatah.jenis', 'jatah')
            ->where('jatah.persetujuan_atasan2', 'disetujui')
            ->whereColumn('attendances.date', '>=', 'jatah.tanggal_mulai')
            ->whereColumn('attendances.date', '<=', 'jatah.tanggal_selesai');

        $izinApproved = DB::table('leave_requests as izin')
            ->selectRaw('1')
            ->whereColumn('izin.employee_id', 'attendances.employee_id')
            ->where('izin.jenis', 'izin')
            ->where('izin.persetujuan_atasan2', 'disetujui')
            ->whereColumn('attendances.date', '>=', 'izin.tanggal_mulai')
            ->whereColumn('attendances.date', '<=', 'izin.tanggal_selesai');

        DB::table('attendances')
            ->where('status', 'izin')
            ->where('method', 'manual')
            ->whereExists($jatahApproved)
            ->whereNotExists($izinApproved)
            ->update(['status' => 'jatah']);
    }

    public function down(): void
    {
        DB::table('attendances')
            ->where('status', 'jatah')
            ->update(['status' => 'izin']);

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha', 'cuti'])
                ->default('hadir')
                ->change();
        });
    }
};
