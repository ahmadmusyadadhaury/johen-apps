<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->enum('status', ['baru', 'diproses', 'dijeda', 'menunggu_konfirmasi', 'selesai', 'ditolak'])->default('baru')->change();
            $table->unsignedBigInteger('durasi_detik')->default(0);
            $table->timestamp('proses_mulai_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->enum('status', ['baru', 'diproses', 'menunggu_konfirmasi', 'selesai', 'ditolak'])->default('baru')->change();
            $table->dropColumn(['durasi_detik', 'proses_mulai_at']);
        });
    }
};
