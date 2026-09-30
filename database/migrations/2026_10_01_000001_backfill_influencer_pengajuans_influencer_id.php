<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menautkan ulang pengajuan berstatus approved yang influencer_id-nya
     * masih NULL. Kolom influencer_id ditambahkan pada migrasi
     * 2026_09_30_000006 tanpa backfill, sehingga semua pengajuan yang dibuat
     * sebelumnya tidak pernah tertaut ke influencer-nya. Akibatnya, saat
     * pengajuan dihapus, influencer beserta monitoring dan pembayarannya
     * tidak ikut terhapus. Migrasi ini idempoten dan hanya mengisi tautan
     * yang masih kosong.
     */
    public function up(): void
    {
        if (! Schema::hasTable('influencer_pengajuans') || ! Schema::hasTable('influencers')) {
            return;
        }

        $claimed = DB::table('influencer_pengajuans')
            ->where('status', 'approved')
            ->where('is_perpanjangan', 0)
            ->whereNotNull('influencer_id')
            ->pluck('influencer_id')
            ->all();

        $linked = 0;

        $proposals = DB::table('influencer_pengajuans')
            ->whereNull('influencer_id')
            ->get();

        foreach ($proposals as $proposal) {
            if ((bool) $proposal->is_perpanjangan) {
                // Perpanjangan mengikuti influencer dari pengajuan asli milik pengaju yang sama.
                $original = DB::table('influencer_pengajuans')
                    ->where('pengaju_id', $proposal->pengaju_id)
                    ->where('is_perpanjangan', 0)
                    ->where('status', 'approved')
                    ->whereNotNull('influencer_id')
                    ->orderBy('id')
                    ->first();

                if ($original) {
                    DB::table('influencer_pengajuans')
                        ->where('id', $proposal->id)
                        ->update(['influencer_id' => $original->influencer_id]);
                    $linked++;
                }

                continue;
            }

            if ($proposal->status !== 'approved') {
                continue;
            }

            // Pengajuan asli: cocokkan dengan influencer berdasarkan nama (+ divisi),
            // dan jangan menautkan influencer yang sudah diklaim pengajuan lain.
            $query = DB::table('influencers')
                ->where('nama', $proposal->nama)
                ->whereNotIn('id', $claimed);

            if ($proposal->divisi === null) {
                $query->whereNull('divisi');
            } else {
                $query->where(function ($q) use ($proposal) {
                    $q->where('divisi', $proposal->divisi)->orWhereNull('divisi');
                });
            }

            $influencer = $query->orderByDesc('mulai_kontrak')->first();

            if ($influencer) {
                DB::table('influencer_pengajuans')
                    ->where('id', $proposal->id)
                    ->update(['influencer_id' => $influencer->id]);
                $linked++;
            }
        }

        logger("[influencer:backfill] Menautkan {$linked} pengajuan approved ke influencer.");
    }

    public function down(): void
    {
        // Tidak ada pemisahan yang aman dan dapat dibalik; biarkan tanpa aksi.
    }
};