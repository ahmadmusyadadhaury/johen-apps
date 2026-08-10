<?php

namespace App\Http\Controllers;

use App\Models\DigitalAsset;
use App\Services\DigitalPaymentApiService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DigitalAssetController extends Controller
{
    public function __construct(private DigitalPaymentApiService $api)
    {
    }

    public function index()
    {
        $apiAssets = $this->api->payments()
            ->sortByDesc(fn ($a) => ($a->jatuh_tempo?->timestamp ?? $a->id ?? 0))
            ->values();

        $assets = $apiAssets->isNotEmpty()
            ? $apiAssets
            : DigitalAsset::with('creator')->latest()->get();

        $totalAset = $assets->count();
        $totalLunas = $assets->filter(fn ($a) => $a->status === 'lunas')->count();
        $totalTerlambat = $assets->filter(
            fn ($a) => $a->status === 'terlambat'
                || ($a->status === 'menunggu' && $a->jatuh_tempo?->lt(today()))
        )->count();
        $totalJatuhTempo = $assets->filter(
            fn ($a) => $a->status === 'menunggu' && $a->jatuh_tempo?->gte(today())
        )->count();

        $totalNominal = $assets->sum('nominal');
        $nominalLunas = $assets->filter(fn ($a) => $a->status === 'lunas')->sum('nominal');
        $nominalTerlambat = $assets->filter(
            fn ($a) => $a->status === 'terlambat'
                || ($a->status === 'menunggu' && $a->jatuh_tempo?->lt(today()))
        )->sum('nominal');
        $nominalMenunggu = $totalNominal - $nominalLunas - $nominalTerlambat;

        $assetsClient = $assets->map(fn ($a) => $this->toClientArray($a))->values();

        return view('digital.index', compact(
            'totalAset', 'totalLunas', 'totalJatuhTempo', 'totalTerlambat',
            'totalNominal', 'nominalLunas', 'nominalMenunggu', 'nominalTerlambat',
            'assets', 'assetsClient'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'tagihan' => 'required|string|max:255',
            'jatuh_tempo' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        DigitalAsset::create($validated);

        return redirect()->route('digital.index')->with('success', 'Tagihan aset digital berhasil ditambahkan.');
    }

    public function update(Request $request, DigitalAsset $digitalAsset)
    {
        $validated = $request->validate([
            'nama_aset' => 'required|string|max:255',
            'tagihan' => 'required|string|max:255',
            'jatuh_tempo' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'status' => 'required|in:lunas,menunggu,terlambat',
            'tgl_bayar' => 'nullable|date',
            'keterangan' => 'nullable|string',
        ]);

        $digitalAsset->update($validated);

        return redirect()->route('digital.index')->with('success', 'Tagihan aset digital berhasil diperbarui.');
    }

    public function destroy(DigitalAsset $digitalAsset)
    {
        $digitalAsset->delete();
        return back()->with('success', 'Tagihan aset digital berhasil dihapus.');
    }

    public function markPaid(DigitalAsset $digitalAsset)
    {
        $digitalAsset->update([
            'status' => 'lunas',
            'tgl_bayar' => today(),
        ]);

        return back()->with('success', 'Tagihan aset digital berhasil ditandai lunas.');
    }

    public function data(Request $request)
    {
        $assets = $this->api->payments();

        if ($assets->isEmpty()) {
            $assets = DigitalAsset::with('creator')->latest()->get();
        }

        if ($request->search) {
            $search = strtolower((string) $request->search);
            $assets = $assets->filter(function ($a) use ($search) {
                return str_contains(strtolower((string) $a->nama_aset), $search)
                    || str_contains(strtolower((string) $a->tagihan), $search)
                    || str_contains(strtolower((string) $a->pic), $search)
                    || str_contains(strtolower((string) $a->email), $search);
            });
        }

        if ($request->status && $request->status !== 'semua') {
            if ($request->status === 'terlambat') {
                $assets = $assets->filter(
                    fn ($a) => $a->status === 'terlambat'
                        || ($a->status === 'menunggu' && $a->jatuh_tempo?->lt(today()))
                );
            } else {
                $assets = $assets->filter(fn ($a) => $a->status === $request->status);
            }
        }

        $assets = $assets
            ->sortByDesc(fn ($a) => ($a->jatuh_tempo?->timestamp ?? $a->id ?? 0))
            ->values();

        $perPage = 20;
        $total = $assets->count();
        $page = max(1, (int) $request->page);
        $items = $assets->forPage($page, $perPage)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->map(fn ($a) => $this->toClientArray($a))->all(),
                'meta' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ]);
        }

        return $assets;
    }

    public function export(Request $request)
    {
        $assets = $this->api->payments();

        if ($assets->isEmpty()) {
            $assets = DigitalAsset::with('creator')->latest()->get();
        }

        if ($request->status && $request->status !== 'semua') {
            $assets = $assets->filter(fn ($a) => $a->status === $request->status);
        }

        $assets = $assets
            ->sortByDesc(fn ($a) => ($a->jatuh_tempo?->timestamp ?? $a->id ?? 0))
            ->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aset Digital');

        $headers = ['No', 'Nama Aset', 'Email', 'Mulai', 'Berakhir', 'Tagihan', 'Jatuh Tempo', 'Hari', 'Nominal', 'PIC', 'Jabatan', 'Keterangan', 'Status', 'Tgl Bayar'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($assets as $idx => $a) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $a->nama_aset ?? '-');
            $sheet->setCellValue('C' . $row, $a->email ?? '-');
            $sheet->setCellValue('D' . $row, $a->mulai?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('E' . $row, $a->berakhir?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('F' . $row, $a->tagihan ?? '-');
            $sheet->setCellValue('G' . $row, $a->jatuh_tempo?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('H' . $row, $a->hari ?? '-');
            $sheet->setCellValue('I' . $row, $a->nominal ?? 0);
            $sheet->setCellValue('J' . $row, $a->pic ?? '-');
            $sheet->setCellValue('K' . $row, $a->jabatan ?? '-');
            $sheet->setCellValue('L' . $row, $a->keterangan ?? '-');
            $sheet->setCellValue('M' . $row, $a->status ?? '-');
            $sheet->setCellValue('N' . $row, $a->tgl_bayar?->format('d/m/Y') ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'aset_digital_' . date('Ymd') . '.xlsx';

        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function toClientArray($a): array
    {
        return [
            'id' => $a->id ?? null,
            'nama_aset' => $a->nama_aset ?? null,
            'email' => $a->email ?? null,
            'mulai' => $a->mulai ? $a->mulai->toISOString() : null,
            'berakhir' => $a->berakhir ? $a->berakhir->toISOString() : null,
            'tagihan' => $a->tagihan ?? null,
            'jatuh_tempo' => $a->jatuh_tempo ? $a->jatuh_tempo->toISOString() : null,
            'hari' => $a->hari ?? null,
            'nominal' => (float) ($a->nominal ?? 0),
            'pic' => $a->pic ?? null,
            'jabatan' => $a->jabatan ?? null,
            'keterangan' => $a->keterangan ?? null,
            'status' => $a->status ?? null,
            'tgl_bayar' => $a->tgl_bayar ? $a->tgl_bayar->toISOString() : null,
            'bukti' => $a->bukti ?? null,
            'creator' => ['name' => $a->creator?->name],
        ];
    }
}