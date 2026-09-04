<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('provinsi', 150)->nullable()->after('alamat');
            $table->string('kota', 150)->nullable()->after('provinsi');
            $table->string('kecamatan', 150)->nullable()->after('kota');
            $table->string('kelurahan', 150)->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['provinsi', 'kota', 'kecamatan', 'kelurahan']);
        });
    }
};
