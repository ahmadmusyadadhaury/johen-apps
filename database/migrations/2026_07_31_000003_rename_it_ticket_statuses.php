<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('it_tickets')->where('status', 'baru')->update(['status' => 'menunggu']);
        DB::table('it_tickets')->where('status', 'menunggu_konfirmasi')->update(['status' => 'dilanjutkan']);

        Schema::table('it_tickets', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'diproses', 'dijeda', 'dilanjutkan', 'selesai', 'ditolak'])->default('menunggu')->change();
        });
    }

    public function down(): void
    {
        DB::table('it_tickets')->where('status', 'menunggu')->update(['status' => 'baru']);
        DB::table('it_tickets')->where('status', 'dilanjutkan')->update(['status' => 'menunggu_konfirmasi']);

        Schema::table('it_tickets', function (Blueprint $table) {
            $table->enum('status', ['baru', 'diproses', 'dijeda', 'menunggu_konfirmasi', 'selesai', 'ditolak'])->default('baru')->change();
        });
    }
};
