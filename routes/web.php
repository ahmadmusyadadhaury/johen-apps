<?php

use App\Http\Controllers\ActivityCompetitorController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AssetViewController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\DailyTrackingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DigitalAssetController;
use App\Http\Controllers\DigitalAssetRegistryController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\ElectricityController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InternetController;
use App\Http\Controllers\IplRukoController;
use App\Http\Controllers\ItTicketController;
use App\Http\Controllers\JadwalMaintenanceController;
use App\Http\Controllers\JobdeskController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PayrollImportController;
use App\Http\Controllers\PayrollPreviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectItController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\StrukturOrganisasiController;
use App\Http\Controllers\WeeklyReportController;
use App\Livewire\AbsensiTable;
use App\Livewire\AdminDailyTrackingTable;
use App\Livewire\AnnouncementTable;
use App\Livewire\BirthdayWishDetail;
use App\Livewire\BirthdayWishTable;
use App\Livewire\ContentPlanTable;
use App\Livewire\CutiIzinTable;
use App\Livewire\FreelanceTable;
use App\Livewire\KalenderEventTable;
use App\Livewire\KontrakKerjaTable;
use App\Livewire\ManualBookTable;
use App\Livewire\PositionTable;
use App\Livewire\PresensiHostLive;
use App\Livewire\PresensiHostRekap;
use App\Livewire\PubgDailyTrackingTable;
use App\Livewire\RunningRateDashboard;
use App\Livewire\RekapStokTable;
use App\Livewire\StockDailyTrackingTable;
use App\Livewire\StokKetersediaanTable;
use App\Livewire\StokTargetTable;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/ucapan-ultah', [DashboardController::class, 'storeBirthdayWish'])->name('dashboard.birthday-wish');
    Route::post('/dashboard/ucapan-ultah/sembunyikan', [DashboardController::class, 'hideBirthdayBanner'])->name('dashboard.birthday-banner.hide');
    Route::get('/dashboard/divisi/{division}', [DashboardController::class, 'division'])->name('dashboard.division');

    Route::prefix('hris')->name('hris.')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/informasi-saya', [EmployeeController::class, 'informasiSaya'])->name('informasi-saya');
        Route::get('/employees/creative', [EmployeeController::class, 'creative'])->name('employees.creative');
        Route::get('/influencer', function () {
            return view('influencer.index');
        })->name('influencer');
        Route::get('/influencer/pengajuan', function () {
            return view('influencer.pengajuan');
        })->name('influencer-pengajuan');
        Route::get('/kalender-event', KalenderEventTable::class)->name('kalender-event');
        Route::get('/employees/{employee}/photo', [EmployeeController::class, 'showPhoto'])->name('employees.photo');
        Route::post('/employees/{employee}/photo', [EmployeeController::class, 'uploadPhoto'])->name('employees.upload-photo');
        Route::post('/employees/{employee}/documents', [EmployeeController::class, 'storeDocument'])->name('employees.store-document');
        Route::get('/employees/{employee}/documents/{document}/download', [EmployeeController::class, 'downloadDocument'])->name('employees.download-document');
        Route::delete('/employees/{employee}/documents/{document}', [EmployeeController::class, 'destroyDocument'])->name('employees.destroy-document');

        Route::post('/employees/{employee}/contracts', [EmployeeController::class, 'storeContract'])->name('employees.store-contract');
        Route::get('/employees/{employee}/contracts/{contract}', [EmployeeController::class, 'getContract'])->name('employees.get-contract');
        Route::delete('/employees/{employee}/contracts/{contract}', [EmployeeController::class, 'destroyContract'])->name('employees.destroy-contract');
        Route::put('/employees/{employee}/contracts/{contract}', [EmployeeController::class, 'updateContract'])->name('employees.update-contract');

        Route::post('/employees/{employee}/position-histories', [EmployeeController::class, 'storePositionHistory'])->name('employees.store-position-history');
        Route::put('/employees/{employee}/position-histories/{positionHistory}', [EmployeeController::class, 'updatePositionHistory'])->name('employees.update-position-history');
        Route::delete('/employees/{employee}/position-histories/{positionHistory}', [EmployeeController::class, 'destroyPositionHistory'])->name('employees.destroy-position-history');

        Route::post('/employees/{employee}/promotions', [PromotionController::class, 'store'])->name('employees.store-promotion');
        Route::delete('/employees/{employee}/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('employees.destroy-promotion');
        Route::get('/employees/{employee}/promotions/{promotion}/download', [PromotionController::class, 'downloadPdf'])->name('employees.download-promotion-pdf');

        Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
        Route::get('/struktur-organisasi', [StrukturOrganisasiController::class, 'index'])->name('struktur-organisasi');
        Route::get('/absensi', AbsensiTable::class)->name('absensi');
        Route::get('/presensi-host', PresensiHostLive::class)->name('presensi-host');
        Route::get('/presensi-host-rekap', PresensiHostRekap::class)->name('presensi-host-rekap');
        Route::get('/cuti-izin', CutiIzinTable::class)->name('cuti-izin');
        Route::get('/kontrak-kerja', KontrakKerjaTable::class)->name('kontrak-kerja');
        Route::get('/freelance', FreelanceTable::class)->name('freelance');
        Route::get('/manual-book', ManualBookTable::class)->name('manual-book');
        Route::get('/pengumuman', AnnouncementTable::class)->name('announcements');
        Route::get('/ucapan-ulang-tahun', BirthdayWishTable::class)->name('birthday-wishes')->middleware('role:super_admin,staff_hr');
        Route::get('/ucapan-ulang-tahun/{employee}', BirthdayWishDetail::class)->name('birthday-wishes.detail')->middleware('role:super_admin,staff_hr');
        Route::post('/pengumuman/{announcement}/dibaca', [AnnouncementController::class, 'markRead'])->name('announcements.mark-read');
        Route::get('/buku-panduan', function () {
            return view('buku-panduan.index');
        })->name('buku-panduan');
        Route::get('/laporan-penjualan', function () {
            return view('laporan-penjualan.index');
        })->name('laporan-penjualan');
        Route::get('/jobdesk', [JobdeskController::class, 'index'])->name('jobdesk');
        Route::get('/weekly-report', [WeeklyReportController::class, 'index'])->name('weekly-report');
        Route::get('/daily-tracking', [DailyTrackingController::class, 'index'])->name('daily-tracking');
        Route::get('/daily-tracking-admin', AdminDailyTrackingTable::class)->name('daily-tracking-admin');
        Route::get('/daily-tracking-stock', StockDailyTrackingTable::class)->name('daily-tracking-stock');
        Route::get('/rekap-stok', RekapStokTable::class)->name('rekap-stok')->middleware('role:super_admin,gm_ceo,koordinator_stock,staff_stock,staff_hr');
        Route::redirect('/stok-masuk', '/hris/rekap-stok');
        Route::redirect('/stok-keluar', '/hris/rekap-stok');
        Route::get('/stok-ketersediaan', StokKetersediaanTable::class)->name('stok-ketersediaan')->middleware('role:super_admin,gm_ceo,koordinator_stock,staff_stock,staff_hr');
        Route::get('/stok-target', StokTargetTable::class)->name('stok-target')->middleware('role:super_admin,gm_ceo,koordinator_stock,staff_stock,staff_hr');
        Route::get('/activity-competitor', [ActivityCompetitorController::class, 'index'])->name('activity-competitor');
        Route::get('/content-plan', ContentPlanTable::class)->name('content-plan');

        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/employees', [ExportController::class, 'employees'])->name('employees');
            Route::get('/divisions', [ExportController::class, 'divisions'])->name('divisions');
            Route::get('/kontrak-kerja', [ExportController::class, 'kontrakKerja'])->name('kontrak-kerja');
        });
    });

    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/upload', [PayrollImportController::class, 'create'])->name('upload');
        Route::post('/upload', [PayrollImportController::class, 'store'])->name('store');
        Route::delete('/{import}', [PayrollImportController::class, 'destroy'])->name('destroy');
        Route::get('/{import}/preview', [PayrollPreviewController::class, 'index'])->name('preview');
        Route::post('/{import}/process-batch', [PayrollController::class, 'processBatch'])->name('process-batch');
        Route::post('/detail/{detail}/retry', [PayrollController::class, 'retryFailed'])->name('retry-failed');
        Route::get('/{import}', [PayrollController::class, 'show'])->name('show');
        Route::get('/detail/{detail}/download', [PayrollController::class, 'downloadPdf'])->name('download-pdf');

        Route::get('/{import}/progress-json', [PayrollController::class, 'progressJson'])->name('progress-json');
        Route::get('/{import}/email-logs', [EmailLogController::class, 'index'])->name('email-logs');
        Route::post('/email-logs/{detail}/retry', [EmailLogController::class, 'retry'])->name('email-retry');
        Route::post('/{import}/retry-all', [EmailLogController::class, 'retryAll'])->name('email-retry-all');
    });

    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('index');
        Route::get('/{import}', [HistoryController::class, 'show'])->name('show');
    });

    Route::prefix('meeting')->name('meeting.')->group(function () {
        Route::get('/jadwal', [MeetingController::class, 'jadwal'])->name('jadwal');
        Route::get('/permintaan', [MeetingController::class, 'permintaan'])->name('permintaan');
        Route::post('/permintaan', [MeetingController::class, 'storePermintaan'])->name('permintaan.store');
        Route::put('/permintaan/{meetingRequest}/setujui', [MeetingController::class, 'setujui'])->name('permintaan.setujui');
        Route::put('/permintaan/{meetingRequest}/tolak', [MeetingController::class, 'tolak'])->name('permintaan.tolak');
    });

    Route::prefix('bonus')->name('bonus.')->group(function () {
        Route::get('/', [BonusController::class, 'index'])->name('index');
    });

    Route::prefix('electricity')->name('electricity.')->group(function () {
        Route::get('/', [ElectricityController::class, 'index'])->name('index');
        Route::get('/topups-data', [ElectricityController::class, 'topupsData'])->name('topups.data');
        Route::get('/checks-data', [ElectricityController::class, 'checksData'])->name('checks.data');
        Route::get('/stats', [ElectricityController::class, 'stats'])->name('stats');
        Route::post('/topups', [ElectricityController::class, 'storeTopup'])->name('store.topup');
        Route::delete('/topups/{electricityTopup}', [ElectricityController::class, 'destroyTopup'])->name('destroy.topup');
        Route::post('/checks', [ElectricityController::class, 'storeCheck'])->name('store.check');
        Route::delete('/checks/{electricityTokenCheck}', [ElectricityController::class, 'destroyCheck'])->name('destroy.check');
        Route::put('/settings', [ElectricityController::class, 'updateSettings'])->name('update.settings');
        Route::get('/export/topups', [ElectricityController::class, 'exportTopups'])->name('export.topups');
        Route::get('/export/checks', [ElectricityController::class, 'exportChecks'])->name('export.checks');
    });

    Route::prefix('internet')->name('internet.')->group(function () {
        Route::get('/', [InternetController::class, 'index'])->name('index');
        Route::get('/payments-data', [InternetController::class, 'paymentsData'])->name('payments.data');
        Route::get('/checks-data', [InternetController::class, 'checksData'])->name('checks.data');
        Route::post('/', [InternetController::class, 'storePayment'])->name('store.payment');
        Route::put('/{internetPayment}', [InternetController::class, 'updatePayment'])->name('update.payment');
        Route::delete('/{internetPayment}', [InternetController::class, 'destroyPayment'])->name('destroy.payment');
        Route::post('/checks', [InternetController::class, 'storeCheck'])->name('store.check');
        Route::delete('/checks/{internetUsageCheck}', [InternetController::class, 'destroyCheck'])->name('destroy.check');
        Route::get('/export/payments', [InternetController::class, 'exportPayments'])->name('export.payments');
        Route::get('/export/checks', [InternetController::class, 'exportChecks'])->name('export.checks');
    });

    Route::prefix('digital')->name('digital.')->group(function () {
        Route::get('/', [DigitalAssetController::class, 'index'])->name('index');
        Route::get('/data', [DigitalAssetController::class, 'data'])->name('data');
        Route::post('/', [DigitalAssetController::class, 'store'])->name('store');
        Route::put('/{digitalAsset}', [DigitalAssetController::class, 'update'])->name('update');
        Route::delete('/{digitalAsset}', [DigitalAssetController::class, 'destroy'])->name('destroy');
        Route::patch('/{digitalAsset}/mark-paid', [DigitalAssetController::class, 'markPaid'])->name('mark-paid');
        Route::get('/export', [DigitalAssetController::class, 'export'])->name('export');
    });

    Route::prefix('ipl')->name('ipl.')->group(function () {
        Route::get('/', [IplRukoController::class, 'index'])->name('index');
        Route::get('/data', [IplRukoController::class, 'data'])->name('data');
        Route::post('/', [IplRukoController::class, 'store'])->name('store');
        Route::put('/{iplRukoPayment}', [IplRukoController::class, 'update'])->name('update');
        Route::delete('/{iplRukoPayment}', [IplRukoController::class, 'destroy'])->name('destroy');
        Route::patch('/{iplRukoPayment}/mark-paid', [IplRukoController::class, 'markPaid'])->name('mark-paid');
        Route::post('/generate', [IplRukoController::class, 'generateYear'])->name('generate');
        Route::get('/export', [IplRukoController::class, 'export'])->name('export');
    });

    Route::prefix('assets')->name('assets.')->group(function () {
        Route::get('/', [AssetViewController::class, 'index'])->name('index');
        Route::get('/{asset}/detail', [AssetViewController::class, 'detail'])->name('detail');
        Route::get('/{category}', [AssetViewController::class, 'index'])->name('category');
    });

    Route::prefix('digital-registries')->name('digital-registries.')->group(function () {
        Route::get('/', [DigitalAssetRegistryController::class, 'index'])->name('index');
    });

    Route::get('/reimbursement', [ReimbursementController::class, 'index'])->name('reimbursement');

    Route::prefix('it')->name('it.')->group(function () {
        Route::get('/tickets', [ItTicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets', [ItTicketController::class, 'store'])->name('tickets.store');
        Route::patch('/tickets/{ticket}', [ItTicketController::class, 'update'])->name('tickets.update');
        Route::delete('/tickets/{ticket}', [ItTicketController::class, 'destroy'])->name('tickets.destroy');
        Route::post('/tickets/{ticket}/feedback', [ItTicketController::class, 'feedback'])->name('tickets.feedback');
        Route::get('/project', [ProjectItController::class, 'index'])->name('project');
        Route::post('/project', [ProjectItController::class, 'store'])->name('project.store');
        Route::patch('/project/{project}', [ProjectItController::class, 'update'])->name('project.update');
        Route::delete('/project/{project}', [ProjectItController::class, 'destroy'])->name('project.destroy');
        Route::post('/project/{project}/feedback', [ProjectItController::class, 'feedback'])->name('project.feedback');
        Route::get('/maintenance', [JadwalMaintenanceController::class, 'index'])->name('maintenance');
        Route::post('/maintenance', [JadwalMaintenanceController::class, 'storeMaintenance'])->name('maintenance.store');
        Route::patch('/maintenance/{schedule}/complete', [JadwalMaintenanceController::class, 'complete'])->name('maintenance.complete');
        Route::patch('/maintenance/{schedule}', [JadwalMaintenanceController::class, 'update'])->name('maintenance.update');
        Route::delete('/maintenance/{schedule}', [JadwalMaintenanceController::class, 'destroy'])->name('maintenance.destroy');
        Route::delete('/maintenance/pc/{pc}', [JadwalMaintenanceController::class, 'destroyPc'])->name('maintenance.pc.destroy');
        Route::post('/maintenance/{schedule}/feedback', [JadwalMaintenanceController::class, 'feedback'])->name('maintenance.feedback');
    });

    Route::prefix('pubg')->name('pubg.')->group(function () {
        Route::get('/daily-tracking', PubgDailyTrackingTable::class)->name('daily-tracking');
        Route::get('/running-rate', RunningRateDashboard::class)->name('running-rate');
    });

    Route::get('/kelola-akun', UserTable::class)->name('kelola-akun')->middleware('role:super_admin');
    Route::get('/kelola-jabatan', PositionTable::class)->name('kelola-jabatan')->middleware('role:super_admin,gm_ceo,staff_hr');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/pin', [ProfileController::class, 'pin'])->name('profile.pin');
    Route::post('/profile/pin', [ProfileController::class, 'updatePin'])->name('profile.pin.update');

});

Route::get('/aset/{code}', [AssetViewController::class, 'publicShow'])->name('aset.public');

require __DIR__.'/auth.php';
