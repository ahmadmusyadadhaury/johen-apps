<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menghapus influencer yang menjadi yatim setelah pengajuan zombie-nya
     * di-purge oleh migrasi sebelumnya: influencer tanpa pengajuan tertaut
     * DAN tanpa satupun monitoring. Influencer jenis ini tidak lagi bisa
     * dipantau maupun dihapus lewat alur normal (sumber pengajuannya sudah
     * hilang), jadi penghapusannya beserta seluruh jadwal pembayaran
     * membersihkan tab Monitoring dan angka statistik.
     */
    public function up(): void
    {
        if (! Schema::hasTable('influencers')
            || ! Schema::hasTable('influencer_pengajuans')
            || ! Schema::hasTable('influencer_monitorings')) {
            return;
        }

        $orphanIds = DB::table('influencers as i')
            ->whereNotIn('i.id', DB::table('influencer_pengajuans')->select('influencer_id')->whereNotNull('influencer_id'))
            ->whereNotIn('i.id', DB::table('influencer_monitorings')->select('influencer_id'))
            ->pluck('i.id');

        if ($orphanIds->isEmpty()) {
            return;
        }

        $affected = [];

        foreach ($orphanIds as $id) {
            $names = DB::table('influencers')->where('id', $id)->value('nama');
            DB::table('influencer_pembayarans')->where('influencer_id', $id)->delete();
            DB::table('influencers')->where('id', $id)->delete();
            $affected[] = "#{$id} ({$names})";
        }

        logger('[influencer:purge] Influencer yatim dihapus: '.implode(', ', $affected).'.');
    }

    public function down(): void
    {
        // Penghapusan data tidak dapat dibalikkan; biarkan tanpa aksi.
    }
};