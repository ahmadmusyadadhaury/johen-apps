<?php

namespace App\Livewire;

use App\Models\ScriptVideoTarget;
use App\Models\ScriptVideoUpload;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarScriptVideoBadge extends Component
{
    public function render()
    {
        $user = auth()->user();
        $badge = 0;

        if ($user) {
            $bulan = ScriptVideoUpload::currentBulanName();
            $tahun = ScriptVideoUpload::currentTahun();
            $target = ScriptVideoTarget::targetFor($bulan, $tahun);
            $hostGame = $user->scriptVideoGameCategory();

            if ($hostGame !== null) {
                $uploadedToday = ScriptVideoUpload::query()
                    ->where('user_id', $user->id)
                    ->where('kategori_game', $hostGame)
                    ->whereDate('created_at', now()->toDateString())
                    ->count();
                $badge = max(0, 2 - $uploadedToday);
            } elseif (in_array($user->role, [
                User::ROLE_MANAGER,
                User::ROLE_GM_CEO,
                User::ROLE_SUPER_ADMIN,
                User::ROLE_ADMIN_MASTER,
            ])) {
                $counts = ScriptVideoUpload::forPeriod($bulan, $tahun)
                    ->selectRaw('user_id, COUNT(*) as jumlah')
                    ->groupBy('user_id')
                    ->pluck('jumlah', 'user_id');

                $badge = User::koordinator()->get()
                    ->filter(fn ($koordinator) => ((int) ($counts[$koordinator->id] ?? 0)) === 0)
                    ->count();
            } elseif ($user->isStaffCreative()) {
                $badge = ScriptVideoUpload::forPeriod($bulan, $tahun)->count();
            } elseif ($user->isAnyKoordinator()) {
                $minggu = ScriptVideoUpload::mingguUntukTanggal(now());
                $uploadedSebelum = ScriptVideoUpload::forPeriod($bulan, $tahun)
                    ->where('user_id', $user->id)
                    ->where('minggu', '<', $minggu)
                    ->count();
                $quota = ScriptVideoTarget::weeklyQuotaFor($target, $uploadedSebelum, $minggu);

                $terisiMinggu = ScriptVideoUpload::forPeriod($bulan, $tahun)
                    ->where('user_id', $user->id)
                    ->where('minggu', $minggu)
                    ->count();

                $badge = max(0, $quota - $terisiMinggu);
            }
        }

        return view('livewire.sidebar-script-video-badge', ['badge' => $badge]);
    }

    #[On('script-video-updated')]
    public function refresh(): void
    {
        //
    }
}
