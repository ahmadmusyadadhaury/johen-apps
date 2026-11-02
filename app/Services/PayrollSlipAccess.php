<?php

namespace App\Services;

use App\Models\PayrollDetail;
use App\Models\User;

class PayrollSlipAccess
{
    public function authorize(User $user, PayrollDetail $detail): void
    {
        $isOwner = $user->employee_id !== null && (int) $user->employee_id === (int) $detail->employee_id;
        $canManagePayroll = $user->isSuperAdmin() || $user->isAdminMaster() || $user->isGmCeo();

        abort_unless($isOwner || $canManagePayroll, 403);
    }

    public function isVerified(User $user, PayrollDetail $detail): bool
    {
        $verifiedUntil = (int) session()->get($this->sessionKey($user, $detail), 0);

        return $verifiedUntil >= now()->timestamp;
    }

    public function markVerified(User $user, PayrollDetail $detail): void
    {
        session()->put($this->sessionKey($user, $detail), now()->addMinutes(5)->timestamp);
    }

    private function sessionKey(User $user, PayrollDetail $detail): string
    {
        return 'payroll_slip_verification.'.$user->id.'.'.$detail->id;
    }
}
