<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('tipe', 20)->after('id')->default('karyawan_aktif')->index();
        });

        DB::table('employees')->where('status', 'aktif')->update(['tipe' => 'karyawan_aktif']);
        DB::table('employees')->where('status', 'nonaktif')->update(['tipe' => 'mantan_karyawan']);
        DB::table('employees')->where('status', 'resign')->update(['tipe' => 'mantan_karyawan']);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif', 'resign'])->default('aktif')->after('id');
        });

        DB::table('employees')->where('tipe', 'karyawan_aktif')->update(['status' => 'aktif']);
        DB::table('employees')->where('tipe', 'calon_karyawan')->update(['status' => 'nonaktif']);
        DB::table('employees')->where('tipe', 'mantan_karyawan')->update(['status' => 'resign']);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
