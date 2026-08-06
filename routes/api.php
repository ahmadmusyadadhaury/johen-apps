<?php

use App\Http\Controllers\Api\AssetCategoryController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeetingApiController;
use App\Http\Controllers\Api\DigitalAssetApiController;
use App\Http\Controllers\Api\ElectricityApiController;
use App\Http\Controllers\Api\InternetApiController;
use App\Http\Controllers\Api\IplRukoApiController;
use App\Http\Controllers\Api\JadwalMeetingApiController;
use App\Http\Controllers\Api\PaymentCategoryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentSubmissionApiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Public meeting schedule endpoint
Route::get('/jadwal-meeting', [JadwalMeetingApiController::class, 'index'])->name('api.jadwal-meeting');

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Meetings
    Route::get('/meetings', [MeetingApiController::class, 'index'])->name('api.meetings');
    Route::post('/meetings', [MeetingApiController::class, 'store'])->name('api.meetings.store');
    Route::get('/meetings/{meeting}', [MeetingApiController::class, 'show'])->name('api.meetings.show');
    Route::put('/meetings/{meeting}', [MeetingApiController::class, 'update'])->name('api.meetings.update');
    Route::delete('/meetings/{meeting}', [MeetingApiController::class, 'destroy'])->name('api.meetings.destroy');
    Route::patch('/meetings/{meeting}/status', [MeetingApiController::class, 'updateStatus'])->name('api.meetings.status');

    // Meeting Requests
    Route::get('/meeting-requests', [MeetingApiController::class, 'meetingRequests'])->name('api.meeting-requests');
    Route::post('/meeting-requests', [MeetingApiController::class, 'storeMeetingRequest'])->name('api.meeting-requests.store');
    Route::put('/meeting-requests/{meetingRequest}/approve', [MeetingApiController::class, 'approveMeetingRequest'])->name('api.meeting-requests.approve');
    Route::put('/meeting-requests/{meetingRequest}/reject', [MeetingApiController::class, 'rejectMeetingRequest'])->name('api.meeting-requests.reject');

    // Asset Categories
    Route::apiResource('asset-categories', AssetCategoryController::class);

    // Assets
    Route::get('/assets', [AssetController::class, 'index']);
    Route::post('/assets', [AssetController::class, 'store']);
    Route::get('/assets/{asset}', [AssetController::class, 'show']);
    Route::put('/assets/{asset}', [AssetController::class, 'update']);
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy']);
    Route::post('/assets/{asset}/photo', [AssetController::class, 'uploadPhoto']);

    // Asset Loans
    Route::get('/assets/{asset}/loans', [AssetController::class, 'loans']);
    Route::post('/assets/{asset}/loans', [AssetController::class, 'storeLoan']);
    Route::put('/asset-loans/{assetLoan}/return', [AssetController::class, 'returnLoan']);

    // Asset Maintenances
    Route::get('/assets/{asset}/maintenances', [AssetController::class, 'maintenances']);
    Route::post('/assets/{asset}/maintenances', [AssetController::class, 'storeMaintenance']);

    // Payment Categories
    Route::apiResource('payment-categories', PaymentCategoryController::class);

    // Payments
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::put('/payments/{payment}', [PaymentController::class, 'update']);
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
    Route::post('/payments/{payment}/proof', [PaymentController::class, 'uploadProof']);
    Route::patch('/payments/{payment}/status', [PaymentController::class, 'updateStatus']);
    Route::get('/payments/statistics', [PaymentController::class, 'statistics']);
    Route::get('/payments/upcoming', [PaymentController::class, 'upcoming']);

    // Electricity
    Route::prefix('electricity')->name('api.electricity.')->group(function () {
        Route::get('/stats', [ElectricityApiController::class, 'stats']);
        Route::get('/topups', [ElectricityApiController::class, 'topups']);
        Route::post('/topups', [ElectricityApiController::class, 'storeTopup']);
        Route::delete('/topups/{electricityTopup}', [ElectricityApiController::class, 'destroyTopup']);
        Route::get('/checks', [ElectricityApiController::class, 'checks']);
        Route::post('/checks', [ElectricityApiController::class, 'storeCheck']);
        Route::delete('/checks/{electricityTokenCheck}', [ElectricityApiController::class, 'destroyCheck']);
        Route::put('/settings', [ElectricityApiController::class, 'updateSettings']);
    });

    // Internet
    Route::prefix('internet')->name('api.internet.')->group(function () {
        Route::get('/payments', [InternetApiController::class, 'payments']);
        Route::post('/payments', [InternetApiController::class, 'storePayment']);
        Route::put('/payments/{internetPayment}', [InternetApiController::class, 'updatePayment']);
        Route::delete('/payments/{internetPayment}', [InternetApiController::class, 'destroyPayment']);
        Route::get('/stats', [InternetApiController::class, 'stats']);
        Route::get('/checks', [InternetApiController::class, 'checks']);
        Route::post('/checks', [InternetApiController::class, 'storeCheck']);
        Route::delete('/checks/{internetUsageCheck}', [InternetApiController::class, 'destroyCheck']);
    });

    // Digital Assets
    Route::apiResource('digital-assets', DigitalAssetApiController::class);
    Route::patch('/digital-assets/{digitalAsset}/mark-paid', [DigitalAssetApiController::class, 'markPaid']);

    // IPL Ruko
    Route::apiResource('ipl-ruko', IplRukoApiController::class);
    Route::patch('/ipl-ruko/{iplRukoPayment}/mark-paid', [IplRukoApiController::class, 'markPaid']);
    Route::post('/ipl-ruko/generate-year', [IplRukoApiController::class, 'generateYear']);

    // Payment Submissions
    Route::prefix('payment-submissions')->name('api.payment-submissions.')->group(function () {
        Route::get('/tagihan', [PaymentSubmissionApiController::class, 'tagihan']);
        Route::get('/pengajuan', [PaymentSubmissionApiController::class, 'pengajuan']);
        Route::get('/persetujuan', [PaymentSubmissionApiController::class, 'persetujuan']);
        Route::post('/', [PaymentSubmissionApiController::class, 'store']);
        Route::get('/{paymentSubmission}', [PaymentSubmissionApiController::class, 'show']);
        Route::patch('/{paymentSubmission}/approve', [PaymentSubmissionApiController::class, 'approve']);
        Route::patch('/{paymentSubmission}/reject', [PaymentSubmissionApiController::class, 'reject']);
        Route::post('/{paymentSubmission}/upload-bukti', [PaymentSubmissionApiController::class, 'uploadBukti']);
        Route::patch('/{paymentSubmission}/mark-paid', [PaymentSubmissionApiController::class, 'markPaid']);
        Route::delete('/{paymentSubmission}', [PaymentSubmissionApiController::class, 'destroy']);
    });
});
