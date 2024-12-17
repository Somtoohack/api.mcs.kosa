<?php

namespace App\Http\Controllers\Redbiller;

use App\Http\Controllers\Controller;
use App\Services\Redbiller\KYCService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KYCController extends Controller
{
    protected KYCService $kycService;

    public function __construct(KYCService $kycService)
    {
        $this->kycService = $kycService;
    }

    /**
     * BVN Lookup
     */
    public function lookupBVN(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->lookupBVN(
                $request->account_no,
                $request->bank_code,
                $request->reference
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify BVN 2.0
     */
    public function verifyBVN2(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bvn' => 'required|string|size:11',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyBVN2($request->bvn, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify BVN 3.0
     */
    public function verifyBVN3(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bvn' => 'required|string|size:11',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyBVN3($request->bvn, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Phone Number 3.0
     */
    public function verifyPhoneNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_no' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyPhoneNumber3($request->phone_no, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify NIN
     */
    public function verifyNIN(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nin' => 'required|string|size:11',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyNIN($request->nin, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Voter's Card
     */
    public function verifyVotersCard(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'vin' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyVotersCard($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify International Passport
     */
    public function verifyPassport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string',
            'passport_no' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyPassport($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Company Registration (CAC)
     */
    public function verifyCAC(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'nullable|string',
            'registration_number' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyCAC($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Tax Identification Number (TIN)
     */
    public function verifyTIN(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tin' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->verifyTIN($request->tin, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Find Bank Account
     */
    public function findBankAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->findBankAccount($request->account_no, $request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Bank Account Lookup
     */
    public function lookupBankAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->lookupBankAccount(
                $request->account_no,
                $request->bank_code,
                $request->reference
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Bank Account Tier
     */
    public function getBankAccountTier(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->getBankAccountTier(
                $request->account_no,
                $request->bank_code
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate Bank Account Tier
     */
    public function validateBankAccountTier(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string',
            'bank_code' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->kycService->validateBankAccountTier(
                $request->account_no,
                $request->bank_code,
                $request->amount
            );
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}