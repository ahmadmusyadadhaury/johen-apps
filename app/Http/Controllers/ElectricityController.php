<?php

namespace App\Http\Controllers;

use App\Models\ElectricitySetting;
use App\Models\ElectricityTokenCheck;
use App\Models\ElectricityTopup;
use App\Services\ElectricityApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ElectricityController extends Controller
{
    public function __construct(private ElectricityApiService $api)
    {
    }

    public function index()
    {
        $setting = ElectricitySetting::firstOrCreate(
            ['id' => 1],
            ['kapasitas_kwh' => 3000, 'updated_by' => auth()->id()]
        );

        $apiTopups = $this->api->topups()
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();

        $lastTopup = $apiTopups->first()
            ?? ElectricityTopup::with('creator')->latest()->first();

        $lastCheck = ElectricityTokenCheck::with('checker')->latest()->first();

        $apiChecks = $this->api->checks();
        $totalTerpakai = $apiChecks->isNotEmpty()
            ? $apiChecks->sum('terpakai')
            : ElectricityTokenCheck::sum('terpakai');

        $sisaApi = $this->api->sisaToken();
        $sisaToken = $sisaApi ?? $lastCheck?->sisa_kwh ?? 0;

        $topups = $apiTopups->isNotEmpty()
            ? $apiTopups
            : ElectricityTopup::with('creator')->latest()->get();

        $checks = $apiChecks->isNotEmpty()
            ? $apiChecks
            : ElectricityTokenCheck::with('checker')->latest()->paginate(20);

        return view('electricity.index', compact(
            'setting', 'lastTopup', 'lastCheck', 'totalTerpakai', 'sisaToken', 'topups', 'checks'
        ));
    }

    public function topupsData(Request $request)
    {
        $topups = $this->api->topups();

        if ($topups->isEmpty()) {
            $topups = ElectricityTopup::with('creator')->latest()->get();
        }

        $filter = $request->filter ?? 'bulanan';
        $topups = $this->applyCollectionFilter($topups, $filter)
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();

        $perPage = 20;
        $total = $topups->count();
        $page = max(1, (int) $request->page);
        $items = $topups->forPage($page, $perPage)->values();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $items->map(fn ($t) => $this->toClientArray($t))->all(),
                'meta' => [
                    'current_page' => $page,
                    'last_page' => (int) ceil($total / $perPage),
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ]);
        }

        return $topups;
    }

    public function storeTopup(Request $request)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'periode' => 'required|string|max:50',
            'jumlah_kwh' => 'required|numeric|min:0',
            'nominal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['created_by'] = auth()->id();

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti-token', 'public');
        }

        ElectricityTopup::create($validated);

        return redirect()->route('electricity.index')->with('success', 'Top up token berhasil dicatat.');
    }

    public function destroyTopup(ElectricityTopup $electricityTopup)
    {
        if ($electricityTopup->bukti && Storage::disk('public')->exists($electricityTopup->bukti)) {
            Storage::disk('public')->delete($electricityTopup->bukti);
        }

        $electricityTopup->delete();

        return back()->with('success', 'Riwayat top up berhasil dihapus.');
    }

    public function checksData(Request $request)
    {
        $checks = $this->api->checks();

        if ($checks->isEmpty()) {
            $checks = ElectricityTokenCheck::with('checker')->latest()->get();
        }

        $filter = $request->filter ?? 'bulanan';
        $checks = $this->applyCollectionFilterChecks($checks, $filter)->values();

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
            'tanggal_check' => 'required|date',
            'sisa_kwh' => 'required|numeric|min:0',
            'terpakai' => 'required|numeric|min:0',
            'status' => 'nullable|in:normal,rendah,habis',
            'catatan' => 'nullable|string',
        ]);

        $validated['checked_by'] = auth()->id();

        if (!isset($validated['status'])) {
            if ($validated['sisa_kwh'] <= 0) {
                $validated['status'] = 'habis';
            } elseif ($validated['sisa_kwh'] <= 500) {
                $validated['status'] = 'rendah';
            } else {
                $validated['status'] = 'normal';
            }
        }

        ElectricityTokenCheck::create($validated);

        return redirect()->route('electricity.index')->with('success', 'Pengecekan token berhasil dicatat.');
    }

    public function destroyCheck(ElectricityTokenCheck $electricityTokenCheck)
    {
        $electricityTokenCheck->delete();

        return back()->with('success', 'Data pengecekan berhasil dihapus.');
    }

    public function stats()
    {
        $setting = ElectricitySetting::firstOrCreate(
            ['id' => 1],
            ['kapasitas_kwh' => 3000, 'updated_by' => auth()->id()]
        );

        $apiTopups = $this->api->topups()
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();

        $lastTopup = $apiTopups->first()
            ?? ElectricityTopup::with('creator')->latest()->first();
        $lastCheck = ElectricityTokenCheck::with('checker')->latest()->first();

        $totalTopupKwh = $apiTopups->isNotEmpty()
            ? $apiTopups->sum('jumlah_kwh')
            : ElectricityTopup::sum('jumlah_kwh');

        $apiChecks = $this->api->checks();
        $totalTerpakai = $apiChecks->isNotEmpty()
            ? $apiChecks->sum('terpakai')
            : ElectricityTokenCheck::sum('terpakai');
        $totalNominal = $apiTopups->isNotEmpty()
            ? $apiTopups->sum('nominal')
            : ElectricityTopup::sum('nominal');

        $sisaApi = $this->api->sisaToken();
        $sisaToken = $sisaApi ?? $lastCheck?->sisa_kwh ?? 0;

        return response()->json([
            'setting' => $setting,
            'last_topup' => $lastTopup,
            'last_check' => $lastCheck,
            'total_topup_kwh' => $totalTopupKwh,
            'total_terpakai' => $totalTerpakai,
            'total_nominal' => $totalNominal,
            'sisa_token' => $sisaToken,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'kapasitas_kwh' => 'required|numeric|min:0',
        ]);

        $setting = ElectricitySetting::firstOrCreate(
            ['id' => 1],
            ['kapasitas_kwh' => 3000, 'updated_by' => auth()->id()]
        );

        $setting->update([
            'kapasitas_kwh' => $validated['kapasitas_kwh'],
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Pengaturan kapasitas token berhasil diupdate.');
    }

    public function exportTopups(Request $request)
    {
        $topups = $this->api->topups();

        if ($topups->isEmpty()) {
            $topups = ElectricityTopup::with('creator')->latest()->get();
        }

        $filter = $request->filter ?? 'bulanan';
        $topups = $this->applyCollectionFilter($topups, $filter)
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Top Up');

        $headers = ['No', 'Tanggal Bayar', 'Periode', 'Jumlah KWH', 'Nominal', 'Oleh', 'Bukti', 'Catatan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($topups as $idx => $t) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $t->tanggal_bayar->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $t->periode);
            $sheet->setCellValue('D' . $row, $t->jumlah_kwh);
            $sheet->setCellValue('E' . $row, $t->nominal);
            $sheet->setCellValue('F' . $row, $t->creator?->name);
            $sheet->setCellValue('G' . $row, $t->bukti_url ?? '-');
            $sheet->setCellValue('H' . $row, $t->catatan);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'riwayat_topup_' . date('Ymd') . '.xlsx';

        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    public function exportChecks(Request $request)
    {
        $checks = $this->api->checks();

        if ($checks->isEmpty()) {
            $checks = ElectricityTokenCheck::with('checker')->latest()->get();
        }

        $filter = $request->filter ?? 'bulanan';
        $checks = $this->applyCollectionFilterChecks($checks, $filter)->values();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pengecekan Token');

        $headers = ['No', 'Tanggal Check', 'Sisa KWH', 'Terpakai', 'Status', 'Pengecek', 'Catatan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
        }

        $row = 2;
        foreach ($checks as $idx => $c) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $c->tanggal_check?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('C' . $row, $c->sisa_kwh);
            $sheet->setCellValue('D' . $row, $c->terpakai ?? '-');
            $sheet->setCellValue('E' . $row, $c->status);
            $sheet->setCellValue('F' . $row, $c->checker?->name);
            $sheet->setCellValue('G' . $row, $c->catatan);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'pengecekan_token_' . date('Ymd') . '.xlsx';

        $temp = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    private function applyCollectionFilter($items, $filter)
    {
        return match ($filter) {
            'harian' => $items->filter(fn ($i) => $i->tanggal_bayar?->isToday()),
            'mingguan' => $items->filter(fn ($i) => $i->tanggal_bayar?->between(now()->startOfWeek(), now()->endOfWeek())),
            default => $items,
        };
    }

    private function applyCollectionFilterChecks($items, $filter)
    {
        return match ($filter) {
            'harian' => $items->filter(fn ($i) => $i->tanggal_check?->isToday()),
            'mingguan' => $items->filter(fn ($i) => $i->tanggal_check?->between(now()->startOfWeek(), now()->endOfWeek())),
            default => $items,
        };
    }

    private function toCheckClientArray($c): array
    {
        return [
            'id' => $c->id ?? null,
            'tanggal_check' => $c->tanggal_check ? $c->tanggal_check->toISOString() : null,
            'sisa_kwh' => (float) ($c->sisa_kwh ?? 0),
            'terpakai' => $c->terpakai !== null ? (float) $c->terpakai : null,
            'status' => $c->status ?? null,
            'checker' => ['name' => $c->checker?->name],
            'catatan' => $c->catatan ?? null,
        ];
    }

    private function toClientArray($t): array
    {
        return [
            'id' => $t->id ?? null,
            'tanggal_bayar' => $t->tanggal_bayar ? $t->tanggal_bayar->toISOString() : null,
            'periode' => $t->periode ?? '-',
            'jumlah_kwh' => (float) ($t->jumlah_kwh ?? 0),
            'nominal' => (float) ($t->nominal ?? 0),
            'creator' => ['name' => $t->creator?->name],
            'catatan' => $t->catatan ?? null,
            'bukti_url' => $t->bukti_url ?? null,
        ];
    }
}
