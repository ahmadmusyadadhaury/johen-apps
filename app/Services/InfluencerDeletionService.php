<?php

namespace App\Services;

use App\Models\Influencer;
use App\Models\InfluencerMonitoring;
use App\Models\InfluencerPembayaran;
use App\Models\InfluencerPengajuan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InfluencerDeletionService
{
    /**
     * Menghapus influencer secara menyeluruh beserta riwayat monitoring,
     * jadwal pembayaran, dan semua pengajuan (termasuk perpanjangan) yang
     * menautkannya — sekaligus dalam satu transaksi.
     *
     * Dipakai dari dua jalur delete (hapus pengajuan & hapus data influencer)
     * agar hasilnya selalu konsisten dan tidak ada pengajuan zombie yang
     * tertinggal.
     */
    public function purgeInfluencer(int $influencerId): void
    {
        DB::transaction(function () use ($influencerId): void {
            $influencer = Influencer::query()->find($influencerId);
            if ($influencer?->kontrak_file_path) {
                Storage::disk('local')->delete($influencer->kontrak_file_path);
            }
            InfluencerMonitoring::query()->where('influencer_id', $influencerId)->delete();
            InfluencerPembayaran::query()->where('influencer_id', $influencerId)->delete();
            InfluencerPengajuan::query()->where('influencer_id', $influencerId)->delete();
            Influencer::query()->whereKey($influencerId)->delete();
        });
    }

    /**
     * Menentukan influencer milik sebuah pengajuan. Tidak hanya mengandalkan
     * kolom influencer_id (yang bisa NULL untuk pengajuan yang dibuat sebelum
     * kolom itu ada), tetapi juga mencocokkan ulang dari nama + divisi, atau
     * dari pengajuan asli milik pengaju yang sama untuk kasus perpanjangan.
     */
    public function resolveInfluencerId(InfluencerPengajuan $pengajuan): ?int
    {
        if ($pengajuan->influencer_id !== null) {
            return Influencer::query()->whereKey($pengajuan->influencer_id)->exists()
                ? (int) $pengajuan->influencer_id
                : null;
        }

        if ($pengajuan->is_perpanjangan) {
            return InfluencerPengajuan::query()
                ->where('pengaju_id', $pengajuan->pengaju_id)
                ->where('is_perpanjangan', false)
                ->where('status', 'approved')
                ->whereNotNull('influencer_id')
                ->orderBy('id')
                ->value('influencer_id');
        }

        return Influencer::query()
            ->where('nama', $pengajuan->nama)
            ->where(function (Builder $q) use ($pengajuan): void {
                if ($pengajuan->divisi === null) {
                    $q->whereNull('divisi');
                } else {
                    $q->where('divisi', $pengajuan->divisi)->orWhereNull('divisi');
                }
            })
            ->orderByDesc('mulai_kontrak')
            ->value('id');
    }
}
