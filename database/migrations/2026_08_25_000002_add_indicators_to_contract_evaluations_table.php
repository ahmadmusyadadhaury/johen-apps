<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_evaluations', function (Blueprint $table) {
            // Disiplin (30)
            $table->tinyInteger('i_kehadiran')->nullable();
            $table->tinyInteger('i_ketepatan_waktu')->nullable();
            $table->tinyInteger('i_kepatuhan_peraturan')->nullable();

            // Kinerja Kerja (45)
            $table->tinyInteger('i_tanggung_jawab')->nullable();
            $table->tinyInteger('i_kualitas_kerja')->nullable();
            $table->tinyInteger('i_produktivitas')->nullable();
            $table->tinyInteger('i_penyelesaian_tugas')->nullable();

            // Sikap Kerja Sama (15)
            $table->tinyInteger('i_komunikasi')->nullable();
            $table->tinyInteger('i_kerja_sama_tim')->nullable();
            $table->tinyInteger('i_inisiatif')->nullable();

            // Hasil Kerja (10)
            $table->tinyInteger('i_pencapaian_target')->nullable();
            $table->tinyInteger('i_penghargaan_sanksi')->nullable();

            // Catatan terstruktur
            $table->text('catatan_kelebihan')->nullable();
            $table->text('catatan_kekurangan')->nullable();
            $table->text('rekomendasi_pengembangan')->nullable();

            // Rencana perpanjangan (saat lulus evaluasi)
            $table->unsignedTinyInteger('perpanjangan_bulan')->nullable();
            $table->date('perpanjangan_mulai')->nullable();
            $table->date('perpanjangan_berakhir')->nullable();

            // Draft vs final
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contract_evaluations', function (Blueprint $table) {
            $columns = [
                'i_kehadiran', 'i_ketepatan_waktu', 'i_kepatuhan_peraturan',
                'i_tanggung_jawab', 'i_kualitas_kerja', 'i_produktivitas', 'i_penyelesaian_tugas',
                'i_komunikasi', 'i_kerja_sama_tim', 'i_inisiatif',
                'i_pencapaian_target', 'i_penghargaan_sanksi',
                'catatan_kelebihan', 'catatan_kekurangan', 'rekomendasi_pengembangan',
                'perpanjangan_bulan', 'perpanjangan_mulai', 'perpanjangan_berakhir',
                'submitted_at',
            ];

            $table->dropColumn($columns);
        });
    }
};
