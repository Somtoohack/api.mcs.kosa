<?php
use App\Http\Controllers\Redbiller\BillPaymentController;
use App\Http\Controllers\Redbiller\KYCController;
use App\Http\Controllers\Redbiller\RedbillerController;
use App\Http\Controllers\Redbiller\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('redbiller')->group(function () {

    Route::prefix('bank')->group(function () {
        Route::post('/transfer', [RedbillerController::class, 'initiateBankTransfer']);
        Route::post('/transfer/verify', [RedbillerController::class, 'verifyBankTransfer']);
        Route::get('/list', [RedbillerController::class, 'getBankList']);
        Route::post('/account/verify', [RedbillerController::class, 'verifyBankAccount']);
    });
    Route::get('/balance', [RedbillerController::class, 'getBalance']);
    Route::get('/statement', [RedbillerController::class, 'getTransactionStatement']);

    // VBA
    Route::prefix('psa')->group(function () {
        Route::post('/create', [RedbillerController::class, 'createPaymentSubAccount']);
        Route::get('/balance/{reference}', [RedbillerController::class, 'getPSABalance']);
    });

    Route::prefix('bills')->group(function () {
        // Airtime
        Route::post('/airtime/purchase', [BillPaymentController::class, 'purchaseAirtime']);
        Route::post('/airtime/verify', [BillPaymentController::class, 'verifyAirtimePurchase']);
        Route::post('/airtime/retry', [BillPaymentController::class, 'retryAirtimePurchase']);
        Route::get('/airtime/retried-trail', [BillPaymentController::class, 'getAirtimeRetriedTrail']);

        // Data
        Route::post('/data/purchase', [BillPaymentController::class, 'purchaseDataPlan']);
        Route::get('/data/plans/{product}', [BillPaymentController::class, 'getDataPlans']);
        Route::post('/data/verify', [BillPaymentController::class, 'verifyDataPlanPurchase']);
        Route::post('/data/retry', [BillPaymentController::class, 'retryDataPlanPurchase']);
        Route::get('/data/retried-trail', [BillPaymentController::class, 'getDataPlanRetriedTrail']);

        Route::post('/electricity/purchase', [BillPaymentController::class, 'purchaseElectricity']);
        Route::post('/electricity/verify-meter', [BillPaymentController::class, 'verifyMeterNumber']);
        Route::post('/electricity/verify-purchase', [BillPaymentController::class, 'verifyElectricityPurchase']);

        // Cable TV
        Route::post('/cable/purchase', [BillPaymentController::class, 'purchaseCableTV']);
        Route::get('/cable/plans/{product}', [BillPaymentController::class, 'getCablePlans']);
        Route::post('/cable/verify-decoder', [BillPaymentController::class, 'verifyDecoderNumber']);
        Route::post('/cable/verify', [BillPaymentController::class, 'verifyCableTVPurchase']);

        // Internet
        Route::post('/internet/purchase', [BillPaymentController::class, 'purchaseInternet']);
        Route::get('/internet/plans/{product}', [BillPaymentController::class, 'getInternetPlans']);
        Route::post('/internet/verify-device', [BillPaymentController::class, 'verifyDeviceNumber']);
        Route::post('/internet/verify', [BillPaymentController::class, 'verifyInternetPurchase']);

        // Betting
        Route::post('/betting/credit', [BillPaymentController::class, 'creditBettingAccount']);
        Route::get('/betting/providers', [BillPaymentController::class, 'getBettingProviders']);
        Route::post('/betting/verify-account', [BillPaymentController::class, 'verifyBettingAccount']);
        Route::post('/betting/verify', [BillPaymentController::class, 'verifyBettingPayment']);
    });

    Route::prefix('kyc')->group(function () {
        // BVN Verification Routes
        Route::post('/bvn/lookup', [KYCController::class, 'lookupBVN']);
        Route::post('/bvn/verify/1.0', [KYCController::class, 'verifyBVN']);
        Route::post('/bvn/verify/2.0', [KYCController::class, 'verifyBVN2']);
        Route::post('/bvn/verify/3.0', [KYCController::class, 'verifyBVN3']);

        // Bank Account Routes
        Route::post('/bank-account/find', [KYCController::class, 'findBankAccount']);
        Route::post('/bank-account/lookup', [KYCController::class, 'lookupBankAccount']);
        Route::post('/bank-account/tier', [KYCController::class, 'getBankAccountTier']);
        Route::post('/bank-account/validate-tier', [KYCController::class, 'validateBankAccountTier']);

        // Identity Verification Routes
        Route::post('/phone/verify', [KYCController::class, 'verifyPhoneNumber']);
        Route::post('/nin/verify', [KYCController::class, 'verifyNIN']);
        Route::post('/voters-card/verify', [KYCController::class, 'verifyVotersCard']);
        Route::post('/passport/verify', [KYCController::class, 'verifyPassport']);
        Route::post('/drivers-license/verify', [KYCController::class, 'verifyDriversLicense']);

    });

    Route::prefix('webhook')->group(function () {
        Route::post('/deposit/verify', [WebhookController::class, 'verifyDepositWebhook']);
        Route::post('/bank-transfer/receive', [WebhookController::class, 'handleBankTransferWebhook']);
        Route::post('/airtime/receive', [WebhookController::class, 'handleAirtimeWebhook']);
        Route::post('/data/receive', [WebhookController::class, 'handleDataWebhook']);
        Route::post('/tv/receive', [WebhookController::class, 'handleTvWebhook']);
        Route::post('/electricity/receive', [WebhookController::class, 'handleElectricityWebhook']);
        Route::post('/betting/receive', [WebhookController::class, 'handleBettingWebhook']);

    });
});
