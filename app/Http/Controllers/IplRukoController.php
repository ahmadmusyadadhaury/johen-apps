<?php

namespace App\Http\Controllers;

use App\Models\IplRukoPayment;
use App\Services\IplRukoApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IplRukoController extends Controller
{
    public function __construct(private IplRukoApiService $api)
    {
    }

    public function index()
    {
        $apiPayments = $this->api->payments()
            ->sortByDesc(fn ($p) => ($p->jatuh_tempo?->timestamp ?? $p->id ?? 0))
            ->values();

        $payments = $apiPayments->isNotEmpty()
            ? $apiPayments
            : IplRukoPayment::with('creator')->latest()->get();

        $totalTagihan = $payments->count();
        $totalLunas = $payments->filter(fn ($p) => $p->status === 'lunas')->count();
        $totalTerlambat = $payments->filter(
            fn ($p) => $p->status === 'terlambat'
                || ($p->status === 'menunggu' && $p->jatuh_tempo?->lt(today()))
        )->count();
        $totalMenunggu = $payments->filter(
            fn ($p) => $p->status === 'menunggu' && $p->jatuh_tempo?->gte(today())
        )->count();

        $paymentsClient = $payments->map(fn ($p) => $this->toClientArray($p))->values();

        return view('ipl.index', compact(
            'totalTagihan', 'totalLunas', 'totalTerlambat', 'totalMenunggu',
            'payments', 'paymentsClient'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode' => 'required|string|max:50',
            'tagihan' => 'required|string|max:255',
            'jatuh_tempo' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        IplRukoPayment::create($validated);

        return redirect()->route('ipl.index')->with('success', 'Tagihan IPL Ruko berhasil ditambahkan.');
    }

    public function update(Request $request, IplRukoPayment $iplRukoPayment)
    {
        $validated = $request->validate([
            'periode' => 'required|string|max:50',
            'tagihan' => 'required|string|max:255',
            'jatuh_tempo' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:lunas,menunggu,terlambat',
            'tgl_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $iplRukoPayment->update($validated);

        return redirect()->route('ipl.index')->with('success', 'Tagihan IPL Ruko berhasil diperbarui.');
    }

    public function destroy(IplRukoPayment $iplRukoPayment)
    {
        $iplRukoPayment->delete();
        return back()->with('success', 'Tagihan IPL Ruko berhasil dihapus.');
    }

    public function markPaid(IplRukoPayment $iplRukoPayment)
    {
        $iplRukoPayment->update([
            'status' => 'lunas',
            'tgl_bayar' => today(),
        ]);

        return back()->with('success', 'Tagihan IPL Ruko berhasil ditandai lunas.');
    }

    public function generateYear(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:2020|max:2100',
            'tagihan' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'jatuh_tempo_hari' => 'required|integer|min:1|max:28',
        ]);

        $tahun = $request->tahun;
        $createdBy = auth()->id();
        $count = 0;

        $namaBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $periode = $namaBulan[$bulan - 1] . ' ' . $tahun;
            $jatuhTempo = Carbon::create($tahun, $bulan, $request->jatuh_tempo_hari);

            $exists = IplRukoPayment::where('periode', $periode)->exists();
            if ($exists) {
                continue;
            }

            IplRukoPayment::create([
                'periode' => $periode,
                'tagihan' => $request->tagihan,
                'jatuh_tempo' => $jatuhTempo,
                'nominal' => $request->nominal,
                'status' => 'menunggu',
                'created_by' => $createdBy,
            ]);

            $count++;
        }

        return redirect()->route('ipl.index')->with('success', "Berhasil generate {$count} tagihan IPL Ruko untuk tahun {$tahun}.");
    }

    public function data(Request $request)
    {
        $payments = $this->api->payments();

        if ($payments->isEmpty()) {
            $payments = IplRukoPayment::with('creator')->latest()->get();
        }

        if ($request->search) {
            $search = strtolower((string) $request->search);
            $payments = $payments->filter(function ($p) use ($search) {
                return str_contains(strtolower((string) $p->periode), $search)
                    || str_contains(strtolower((string) $p->tagihan), $search);
            });
        }

        if ($request->status && $request->status !== 'semua') {
            if ($request->status === 'terlambat') {
                $payments = $payments->filter(
                    fn ($p) => $p->status === 'terlambat'
                        || ($p->status === 'menunggu' && $p->jatuh_tempo?->lt(today()))
                );
            } else {
                $payments = $payments->filter(fn ($p) => $p->status === $request->status);
            }
        }

        $payments = $payments
            ->sortByDesc(fn ($p) => ($p->jatuh_tempo?->timestamp ?? $p->id ?? 0))
            ->values();

        $perPage = 20;
        $total = $payments->count();
        $page = max(1, (int) $request->page);
        $items = $payments->forPage($page, $perPage)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->map(fn ($p) => $this->toClientArray($p))->all(),
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

    public function export(Request $request)
    {
        $payments = $this->api->payments();

        if ($payments->isEmpty()) {
            $payments = IplRukoPayment::with('creator')->latest()->get();
        }

        if ($request->status && $request->status !== 'semua') {
            $payments = $payments->filter(fn ($p) => $p->status === $request->status);
        }

        $payments = $payments
            ->sortByDesc(fn ($p) => ($p->jatuh_tempo?->timestamp ?? $p->id ?? 0))
            ->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IPL Ruko');

        $headers = ['No', 'Periode', 'Tagihan', 'Jatuh Tempo', 'Hari', 'Nominal', 'Status', 'Tgl Bayar'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($payments as $idx => $p) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $p->periode ?? '-');
            $sheet->setCellValue('C' . $row, $p->tagihan ?? '-');
            $sheet->setCellValue('D' . $row, $p->jatuh_tempo?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('E' . $row, $p->hari ?? '-');
            $sheet->setCellValue('F' . $row, $p->nominal ?? 0);
            $sheet->setCellValue('G' . $row, $p->status ?? '-');
            $sheet->setCellValue('H' . $row, $p->tgl_bayar?->format('d/m/Y') ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'ipl_ruko_' . date('Ymd') . '.xlsx';

        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function toClientArray($p): array
    {
        return [
            'id' => $p->id ?? null,
            'periode' => $p->periode ?? null,
            'tagihan' => $p->tagihan ?? null,
            'jatuh_tempo' => $p->jatuh_tempo ? $p->jatuh_tempo->toISOString() : null,
            'hari' => $p->hari ?? null,
            'nominal' => (float) ($p->nominal ?? 0),
            'status' => $p->status ?? null,
            'tgl_bayar' => $p->tgl_bayar ? $p->tgl_bayar->toISOString() : null,
            'bukti' => $p->bukti ?? null,
            'creator' => ['name' => $p->creator?->name],
        ];
    }
}