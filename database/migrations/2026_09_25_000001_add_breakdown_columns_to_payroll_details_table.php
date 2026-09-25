<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->decimal('bonus_absensi_full', 15, 2)->default(0)->after('tambahan_upah');
            $table->decimal('pengembalian', 15, 2)->default(0)->after('bonus_absensi_full');
            $table->decimal('tips_pelanggan', 15, 2)->default(0)->after('pengembalian');
            $table->decimal('insentif_creative', 15, 2)->default(0)->after('tips_pelanggan');
            $table->decimal('tambahan_upah_sold', 15, 2)->default(0)->after('premi_bpjs_kesehatan');
            $table->decimal('potongan_absensi_ketidakhadiran', 15, 2)->default(0)->after('potongan_absensi');
            $table->decimal('potongan_absensi_keterlambatan', 15, 2)->default(0)->after('potongan_absensi_ketidakhadiran');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn([
                'bonus_absensi_full', 'pengembalian', 'tips_pelanggan', 'insentif_creative',
                'tambahan_upah_sold', 'potongan_absensi_ketidakhadiran', 'potongan_absensi_keterlambatan',
            ]);
        });
    }
};