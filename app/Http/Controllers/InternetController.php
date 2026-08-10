<?php

namespace App\Http\Controllers;

use App\Models\InternetPayment;
use App\Models\InternetUsageCheck;
use App\Services\InternetApiService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InternetController extends Controller
{
    public function __construct(private InternetApiService $api)
    {
    }

    public function index()
    {
        $apiPayments = $this->api->payments()
            ->sortByDesc(fn ($p) => ($p->masa_tenggang?->timestamp ?? $p->id ?? 0))
            ->values();

        $payments = $apiPayments->isNotEmpty()
            ? $apiPayments
            : InternetPayment::with('creator')->latest()->get();

        $totalWifi = $payments->count();
        $sudahDibayar = $payments->filter(fn ($p) => $p->status === 'lunas')->count();
        $jatuhTempo = $payments->filter(
            fn ($p) => $p->status === 'menunggu' && $p->masa_tenggang?->gte(now())
        )->count();
        $terlambat = $payments->filter(
            fn ($p) => $p->status === 'terlambat'
                || ($p->status === 'menunggu' && $p->masa_tenggang?->lt(now()))
        )->count();

        $apiChecks = $this->api->checks()
            ->sortByDesc(fn ($c) => ($c->tanggal?->timestamp ?? $c->id ?? 0))
            ->values();

        $checks = $apiChecks->isNotEmpty()
            ? $apiChecks
            : InternetUsageCheck::with('checker')->latest()->get();

        return view('internet.index', compact(
            'totalWifi', 'sudahDibayar', 'jatuhTempo', 'terlambat',
            'payments', 'checks'
        ));
    }

    public function paymentsData(Request $request)
    {
        $payments = $this->api->payments();

        if ($payments->isEmpty()) {
            $payments = InternetPayment::with('creator')->latest()->get();
        }

        if ($request->search) {
            $search = strtolower((string) $request->search);
            $payments = $payments->filter(function ($p) use ($search) {
                return str_contains(strtolower((string) $p->nama_internet), $search)
                    || str_contains(strtolower((string) $p->provider), $search)
                    || str_contains(strtolower((string) $p->pic), $search);
            });
        }

        if ($request->status && $request->status !== 'semua') {
            $payments = $payments->filter(fn ($p) => $p->status === $request->status);
        }

        $payments = $payments
            ->sortByDesc(fn ($p) => ($p->masa_tenggang?->timestamp ?? $p->id ?? 0))
            ->values();

        $perPage = 20;
        $total = $payments->count();
        $page = max(1, (int) $request->page);
        $items = $payments->forPage($page, $perPage)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->map(fn ($p) => $this->toPaymentClientArray($p))->all(),
                'meta' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ]);
        }

        return $payments;
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'nama_internet' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'masa_tenggang' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'status' => 'nullable|in:lunas,menunggu,terlambat',
            'tgl_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        InternetPayment::create($validated);

        return redirect()->route('internet.index')->with('success', 'Tagihan internet berhasil ditambahkan.');
    }

    public function updatePayment(Request $request, InternetPayment $internetPayment)
    {
        $validated = $request->validate([
            'nama_internet' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'masa_tenggang' => 'required|date',
            'biaya' => 'required|numeric|min:0',
            'status' => 'nullable|in:lunas,menunggu,terlambat',
            'tgl_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $internetPayment->update($validated);

        return redirect()->route('internet.index')->with('success', 'Tagihan internet berhasil diupdate.');
    }

    public function destroyPayment(InternetPayment $internetPayment)
    {
        $internetPayment->delete();
        return back()->with('success', 'Tagihan internet berhasil dihapus.');
    }

    public function checksData(Request $request)
    {
        $checks = $this->api->checks();

        if ($checks->isEmpty()) {
            $checks = InternetUsageCheck::with('checker')->latest()->get();
        }

        if ($request->month && $request->year) {
            $month = (int) $request->month;
            $year = (int) $request->year;
            $checks = $checks->filter(function ($c) use ($month, $year) {
                if (!$c->tanggal) {
                    return false;
                }
                return $c->tanggal->month === $month && $c->tanggal->year === $year;
            });
        }

        $checks = $checks
            ->sortByDesc(fn ($c) => ($c->tanggal?->timestamp ?? $c->id ?? 0))
            ->values();

        $perPage = 20;
        $total = $checks->count();
        $page = max(1, (int) $request->page);
        $items = $checks->forPage($page, $perPage)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->map(fn ($c) => $this->toCheckClientArray($c))->all(),
                'meta' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ]);
        }

        return $checks;
    }

    public function storeCheck(Request $request)
    {
        $validated = $request->validate([
            'ruangan' => 'required|string|max:255',
            'hari' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'penggunaan_wifi' => 'required|numeric|min:0',
            'penggunaan_ethernet' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['checked_by'] = auth()->id();
        InternetUsageCheck::create($validated);

        return redirect()->route('internet.index')->with('success', 'Pengecekan usage internet berhasil dicatat.');
    }

    public function destroyCheck(InternetUsageCheck $internetUsageCheck)
    {
        $internetUsageCheck->delete();
        return back()->with('success', 'Data pengecekan berhasil dihapus.');
    }

    public function exportPayments(Request $request)
    {
        $payments = $this->api->payments();

        if ($payments->isEmpty()) {
            $payments = InternetPayment::with('creator')->latest()->get();
        }

        if ($request->status && $request->status !== 'semua') {
            $payments = $payments->filter(fn ($p) => $p->status === $request->status);
        }

        $payments = $payments
            ->sortByDesc(fn ($p) => ($p->masa_tenggang?->timestamp ?? $p->id ?? 0))
            ->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pembayaran Internet');

        $headers = ['No', 'Nama Internet', 'Provider', 'PIC', 'Jabatan', 'Masa Tenggang', 'Hari', 'Biaya', 'Status', 'Tgl Bayar'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($payments as $idx => $p) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $p->nama_internet ?? '-');
            $sheet->setCellValue('C' . $row, $p->provider ?? '-');
            $sheet->setCellValue('D' . $row, $p->pic ?? '-');
            $sheet->setCellValue('E' . $row, $p->jabatan ?? '-');
            $sheet->setCellValue('F' . $row, $p->masa_tenggang?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('G' . $row, $p->hari ?? '-');
            $sheet->setCellValue('H' . $row, $p->biaya ?? 0);
            $sheet->setCellValue('I' . $row, $p->status ?? '-');
            $sheet->setCellValue('J' . $row, $p->tgl_bayar?->format('d/m/Y') ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'pembayaran_internet_' . date('Ymd') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    public function exportChecks(Request $request)
    {
        $checks = $this->api->checks();

        if ($checks->isEmpty()) {
            $checks = InternetUsageCheck::with('checker')->latest()->get();
        }

        if ($request->month && $request->year) {
            $month = (int) $request->month;
            $year = (int) $request->year;
            $checks = $checks->filter(function ($c) use ($month, $year) {
                if (!$c->tanggal) {
                    return false;
                }
                return $c->tanggal->month === $month && $c->tanggal->year === $year;
            });
        }

        $checks = $checks
            ->sortByDesc(fn ($c) => ($c->tanggal?->timestamp ?? $c->id ?? 0))
            ->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Usage Internet');

        $headers = ['No', 'Ruangan', 'Hari', 'Tanggal', 'Penggunaan Wifi', 'Penggunaan Ethernet', 'Pengecek', 'Keterangan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($checks as $idx => $c) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $c->ruangan ?? '-');
            $sheet->setCellValue('C' . $row, $c->hari ?? '-');
            $sheet->setCellValue('D' . $row, $c->tanggal?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('E' . $row, $c->penggunaan_wifi ?? 0);
            $sheet->setCellValue('F' . $row, $c->penggunaan_ethernet ?? 0);
            $sheet->setCellValue('G' . $row, $c->checker?->name);
            $sheet->setCellValue('H' . $row, $c->keterangan ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'usage_internet_' . date('Ymd') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function toPaymentClientArray($p): array
    {
        return [
            'id' => $p->id ?? null,
            'nama_internet' => $p->nama_internet ?? null,
            'provider' => $p->provider ?? null,
            'pic' => $p->pic ?? null,
            'jabatan' => $p->jabatan ?? null,
            'masa_tenggang' => $p->masa_tenggang ? $p->masa_tenggang->toISOString() : null,
            'hari' => $p->hari ?? $p->hari_internet ?? '-',
            'biaya' => (float) ($p->biaya ?? 0),
            'status' => $p->status ?? null,
            'tgl_bayar' => $p->tgl_bayar ? $p->tgl_bayar->toISOString() : null,
            'keterangan' => $p->keterangan ?? null,
            'creator' => ['name' => $p->creator?->name],
        ];
    }

    private function toCheckClientArray($c): array
    {
        return [
            'id' => $c->id ?? null,
            'ruangan' => $c->ruangan ?? null,
            'hari' => $c->hari ?? null,
            'tanggal' => $c->tanggal ? $c->tanggal->toISOString() : null,
            'penggunaan_wifi' => (float) ($c->penggunaan_wifi ?? 0),
            'penggunaan_ethernet' => (float) ($c->penggunaan_ethernet ?? 0),
            'keterangan' => $c->keterangan ?? null,
            'checker' => ['name' => $c->checker?->name],
        ];
    }
}