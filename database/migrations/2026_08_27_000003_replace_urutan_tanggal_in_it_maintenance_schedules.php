<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('urutan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        DB::table('it_maintenance_schedules')->orderBy('id')->each(function ($row) {
            DB::table('it_maintenance_schedules')->where('id', $row->id)->update([
                'tanggal_mulai' => $row->tanggal,
            ]);
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn(['urutan', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->integer('urutan')->after('pc_id');
            $table->date('tanggal')->nullable()->after('urutan');
        });

        DB::table('it_maintenance_schedules')->orderBy('id')->each(function ($row) {
            DB::table('it_maintenance_schedules')->where('id', $row->id)->update([
                'tanggal' => $row->tanggal_mulai,
            ]);
        });

        Schema::table('it_maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
