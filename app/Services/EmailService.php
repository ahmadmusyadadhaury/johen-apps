<?php

namespace App\Services;

use App\Mail\PayrollSlipMail;
use App\Facades\FileVault;
use App\Models\EmailLog;
use App\Models\PayrollDetail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailService
{
    public function send(PayrollDetail $detail, string $periode): void
    {
        $emailLog = EmailLog::create([
            'payroll_detail_id' => $detail->id,
            'email' => $detail->email,
            'status' => 'pending',
        ]);

        try {
            if (! Storage::disk('local')->exists($detail->pdf_path)) {
                FileVault::get('local', $detail->pdf_path);
            }
            $pdfPath = Storage::disk('local')->path($detail->pdf_path);

            Mail::to($detail->email)->send(new PayrollSlipMail(
                nama: $detail->nama,
                periode: $periode,
                pdfPath: $pdfPath,
                pdfPassword: $detail->pdf_password,
            ));

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $detail->update(['status' => 'sent']);
        } catch (\Exception $e) {
            $emailLog->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $detail->update(['status' => 'failed']);
        }
    }

    public function retry(PayrollDetail $detail, string $periode): void
    {
        $detail->emailLog()->delete();
        $detail->update(['status' => 'pending']);
        $this->send($detail, $periode);
    }
}
