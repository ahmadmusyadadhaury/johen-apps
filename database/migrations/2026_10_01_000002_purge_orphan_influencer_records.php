<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membersihkan sisa data yatim yang lahir sebelum tautan influencer_id
     * di-isi meyakinkan (lihat migrasi backfill sebelumnya) dan dari
     * penghapusan yang hanya mengandalkan cascade database:
     *   - monitoring & pembayaran yang menunjuk influencer yang sudah hilang;
     *   - pengajuan yang menunjuk influencer yang sudah tidak ada;
     *   - pengajuan approved yang tersisa tetap NULL setelah backfill (zombie,
     *     influencer-nya sudah tidak ada sehingga tidak pernah bisa dimonitor).
     * Semua penghapusan dicatat ke log supaya ada jejak audit.
     */
    public function up(): void
    {
        $log = [];

        if (Schema::hasTable('influencer_pembayarans') && Schema::hasTable('influencers')) {
            $n = DB::table('influencer_pembayarans')
                ->whereNotIn('influencer_id', DB::table('influencers')->select('id'))
                ->delete();
            if ($n > 0) {
                $log[] = "pembayaran yatim: {$n}";
            }
        }

        if (Schema::hasTable('influencer_monitorings') && Schema::hasTable('influencers')) {
            $n = DB::table('influencer_monitorings')
                ->whereNotIn('influencer_id', DB::table('influencers')->select('id'))
                ->delete();
            if ($n > 0) {
                $log[] = "monitoring yatim: {$n}";
            }
        }

        if (Schema::hasTable('influencer_pengajuans') && Schema::hasTable('influencers')) {
            // Pengajuan yang menunjuk influencer yang sudah tidak ada (fallback
            // keamanan jika FK nullOnDelete belum dibuat di database lama).
            $n = DB::table('influencer_pengajuans')
                ->whereNotNull('influencer_id')
                ->whereNotIn('influencer_id', DB::table('influencers')->select('id'))
                ->delete();
            if ($n > 0) {
                $log[] = "pengajuan dengan influencer hilang: {$n}";
            }
        }

        if (Schema::hasTable('influencer_pengajuans')) {
            // Pengajuan approved yang tetap tanpa influencer setelah backfill:
            // influencer-nya sudah tidak ada, jadi ini hanya sisa zombie.
            $n = DB::table('influencer_pengajuans')
                ->where('status', 'approved')
                ->whereNull('influencer_id')
                ->delete();
            if ($n > 0) {
                $log[] = "pengajuan approved zombie: {$n}";
            }
        }

        logger('[influencer:purge] '.implode(', ', $log).(empty($log) ? 'tidak ada data yatim.' : '.'));
    }

    public function down(): void
    {
        // Penghapusan data tidak dapat dibalikkan; biarkan tanpa aksi.
    }
};