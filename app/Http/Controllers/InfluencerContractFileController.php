<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Models\InfluencerPengajuan;
use App\Support\InfluencerPengajuanRouting;
use Illuminate\Support\Facades\Storage;

class InfluencerContractFileController extends Controller
{
    public function show(Influencer $influencer)
    {
        $user = auth()->user();
        $approvedSubmission = InfluencerPengajuan::query()
            ->where('influencer_id', $influencer->id)
            ->where('status', 'approved')
            ->exists();

        abort_unless($approvedSubmission, 404);

        $isOwnKolSubmission = InfluencerPengajuan::query()
            ->where('influencer_id', $influencer->id)
            ->where('pengaju_id', $user->id)
            ->where('status', 'approved')
            ->exists();
        $canReview = $user->isKoordinatorCreative()
            || $user->isHeadOfStore()
            || $user->isGmCeo()
            || $user->isSuperAdminLike();

        abort_unless(
            ($isOwnKolSubmission && InfluencerPengajuanRouting::isKolSubmitter($user)) || $canReview,
            403,
        );
        abort_unless($influencer->kontrak_file_path, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($influencer->kontrak_file_path), 404);

        $path = $disk->path($influencer->kontrak_file_path);
        $mimeType = $disk->mimeType($influencer->kontrak_file_path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="kontrak-influencer-'.$influencer->id.'.'.pathinfo($path, PATHINFO_EXTENSION).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
