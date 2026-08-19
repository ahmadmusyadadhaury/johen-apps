<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('ukuran_baju', 10)->nullable()->after('jenis_kelamin');
            $table->string('agama', 50)->nullable()->after('ukuran_baju');
            $table->string('pendidikan_terakhir', 100)->nullable()->after('agama');
            $table->string('informasi_lowongan', 100)->nullable()->after('no_bpjs');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['ukuran_baju', 'agama', 'pendidikan_terakhir', 'informasi_lowongan']);
        });
    }
};