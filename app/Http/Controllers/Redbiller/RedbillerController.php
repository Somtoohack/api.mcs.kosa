<?php

namespace App\Http\Controllers\Redbiller;

use App\Http\Controllers\Controller;
use App\Services\Redbiller\RedbillerService;
use App\Services\Redbiller\TransactionAlertService;
use App\Services\Traits\RedbillerValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class RedbillerController extends Controller
{
    use RedbillerValidation;

    protected RedbillerService $redbillerService;
    protected TransactionAlertService $transactionAlertService;

    public function __construct(RedbillerService $redbillerService, TransactionAlertService $transactionAlertService)
    {
        $this->redbillerService = $redbillerService;
        $this->transactionAlertService = $transactionAlertService;
    }

    /**
     * Initiate bank transfer
     */
    public function initiateBankTransfer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
            'amount' => 'required|numeric',
            'narration' => 'required|string',
            'callback_url' => 'nullable|url',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!$this->validateReference($request->reference)) {
            return response()->json(['error' => 'Reference exceeds maximum length of 250 characters'], 422);
        }

        try {
            $result = $this->redbillerService->initiateBankTransfer($request->all());

            // Check and notify for insufficient balance
            $this->transactionAlertService->checkAndNotify($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify bank transfer status
     */

    public function verifyBankTransfer(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->redbillerService->verifyBankTransfer($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get bank list
     */
    public function getBankList(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:Commercial,microfinance,mortgage,merchant',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Define a cache key based on the request type
        $cacheKey = 'bank_list';

        // Attempt to retrieve the bank list from the cache
        $result = Cache::remember($cacheKey, 60 * 60, function () use ($request) {
            return $this->redbillerService->getBankList($request->type ?? 'Commercial');
        });

        return response()->json($result);
    }

    /**
     * Get account balance
     */
    public function getBalance(): JsonResponse
    {
        try {
            $result = $this->redbillerService->getBalance();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify bank account
     */
    public function verifyBankAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->redbillerService->verifyBankAccount(
                $request->account_no,
                $request->bank_code
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Payment Sub-Account
     */
    public function createPaymentSubAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bank' => 'required|string',
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'phone_no' => 'required|string',
            'email' => 'required|email',
            'bvn' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'auto_settlement' => 'nullable|string',
            'callback_url' => 'nullable|url',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->redbillerService->createPaymentSubAccount($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}