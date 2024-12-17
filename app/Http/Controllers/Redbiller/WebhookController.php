<?php

namespace App\Http\Controllers\Redbiller;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Services\Redbiller\BillPaymentService;
use App\Services\Redbiller\RedbillerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class WebhookController extends Controller
{

    protected RedbillerService $redbillerService;
    protected BillPaymentService $billPaymentService;

    public function __construct(RedbillerService $redbillerService, BillPaymentService $billPaymentService)
    {
        $this->redbillerService = $redbillerService;
        $this->billPaymentService = $billPaymentService;

    }

    /**
     * Verify bank transfer status
     */

    public function handleBankTransferWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;
            $result = $this->redbillerService->verifyBankTransfer($reference);

            // Log the result of the bank transfer verification
            if ($result['status'] === 'true') {
                Log::info('Bank transfer verified successfully for reference: ' . $reference, [
                    'verification_result' => $result,
                ]);
                return response()->json($result);
            } else {
                $this->handlePaymentFailed($data);
                Log::warning('Bank transfer verification failed for reference: ' . $reference, [
                    'verification_result' => $result,
                ]);
                return response()->json(['error' => 'Bank transfer verification failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error verifying bank transfer: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleDepositWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        try {

            Log::info($data);
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            // Verify the deposit using the reference
            $verificationResult = $this->redbillerService->verifyDeposit($reference);

            // Log the result of the deposit verification
            if ($verificationResult['status'] === 'true' && $verificationResult['details']['status'] === 'Approved') {
                Log::info('Deposit verified successfully for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return response()->json($verificationResult);
            } else {
                $this->handlePaymentFailed($data);
                Log::warning('Deposit verification failed for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['error' => 'Deposit verification failed.'], 400);
            }
        } catch (Exception $e) {
            Log::error('Error verifying deposit: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function handlePaymentFailed($data)
    {
        // Extract relevant information from the webhook data
        $reference = $data['reference'] ?? null;

        // Example: Update the database with the payment failure information
        try {
            // Update the payment record in the database to mark it as failed
            $payment = Deposit::where('reference', $reference)->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            // Notify the user about the failure
            // Notification::send($user, new PaymentFailedNotification($payment));

        } catch (\Exception $e) {
            // Log the error if something goes wrong
            Log::error('Error handling payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle airtime payment webhook.
     */
    public function handleAirtimeWebhook(Request $request): JsonResponse
    {
        Log::info($request->all());

        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            $verificationResult = $this->billPaymentService->verifyAirtimePurchase($reference);
            if ($verificationResult['status'] === 'true' && $verificationResult['response'] === 200) {
                Log::info('Airtime processed successfully.', [
                    'data' => $details,
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['message' => 'Airtime processed successfully.'], 200);
            } else {
                $this->handlePaymentFailed($details);
                Log::warning('Airtime payment failed.', ['data' => $details]);
                return response()->json(['message' => 'Airtime payment failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing airtime webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing airtime.'], 500);
        }
    }

/**
 * Handle data payment webhook.
 */
    public function handleDataWebhook(Request $request): JsonResponse
    {

        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            $verificationResult = $this->billPaymentService->verifyDataPlanPurchase($reference);
            if ($verificationResult['status'] === 'true' && $verificationResult['response'] === 200) {
                Log::info('Data processed successfully.', [
                    'data' => $details,
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['message' => 'Data processed successfully.'], 200);
            } else {
                $this->handlePaymentFailed($details);
                Log::warning('Data payment failed.', ['data' => $details]);
                return response()->json(['message' => 'Data payment failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing data webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing data.'], 500);
        }
    }

/**
 * Handle TV payment webhook.
 */
    public function handleTvWebhook(Request $request): JsonResponse
    {

        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            $verificationResult = $this->billPaymentService->verifyCableTVPurchase($reference);
            if ($verificationResult['status'] === 'true' && $verificationResult['response'] === 200) {
                Log::info('TV processed successfully.', [
                    'data' => $details,
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['message' => 'TV processed successfully.'], 200);
            } else {
                $this->handlePaymentFailed($details);
                Log::warning('TV payment failed.', ['data' => $details]);
                return response()->json(['message' => 'TV payment failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing TV webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing TV.'], 500);
        }
    }

/**
 * Handle electricity payment webhook.
 */
    public function handleElectricityWebhook(Request $request): JsonResponse
    {

        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            $verificationResult = $this->billPaymentService->verifyElectricityPurchase($reference);
            if ($verificationResult['status'] === 'true' && $verificationResult['response'] === 200) {
                Log::info('Electricity processed successfully.', [
                    'data' => $details,
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['message' => 'Electricity processed successfully.'], 200);
            } else {
                $this->handlePaymentFailed($details);
                Log::warning('Electricity payment failed.', ['data' => $details]);
                return response()->json(['message' => 'Electricity payment failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing electricity webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing electricity.'], 500);
        }
    }

/**
 * Handle betting payment webhook.
 */
    public function handleBettingWebhook(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            $details = $request->input('details');
            $reference = $details['reference'] ?? null;

            $verificationResult = $this->billPaymentService->verifyBettingPayment($reference);
            if ($verificationResult['status'] === 'true' && $verificationResult['response'] === 200) {
                Log::info('Betting processed successfully.', [
                    'data' => $details,
                    'verification_result' => $verificationResult,
                ]);
                return response()->json(['message' => 'Betting processed successfully.'], 200);
            } else {
                $this->handlePaymentFailed($details);
                Log::warning('Betting payment failed.', ['data' => $details]);
                return response()->json(['message' => 'Betting payment failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error processing betting webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing betting.'], 500);
        }
    }
}