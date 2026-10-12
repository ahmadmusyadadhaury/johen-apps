<?php

namespace App\Services;

use App\Facades\FileVault;
use App\Models\PayrollDetail;
use Dompdf\Dompdf;

class PdfGenerationService
{
    public function generate(PayrollDetail $detail, string $periode, string $password = ''): string
    {
        $filename = sprintf('%s_%s_%s.pdf', $detail->nik, str_replace(' ', '', $periode), $detail->nama);
        $path = "payroll/{$detail->payroll_import_id}/{$filename}";

        $logoPath = public_path('logo.png');
        $logo = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : '';

        $html = view('pdf.slip-gaji', [
            'detail' => $detail,
            'periode' => $periode,
            'logo' => $logo,
        ])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($password) {
            $canvas = $dompdf->getCanvas();
            $canvas->get_cpdf()->setEncryption($password, $password, []);
        }

        FileVault::put('public', $path, $dompdf->output(), $filename, 'application/pdf');

        return $path;
    }
}
