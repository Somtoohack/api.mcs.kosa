<?php
namespace App\Http\Controllers\Redbiller;

use App\Http\Controllers\Controller;
use App\Mail\InsufficientBalanceAlert;
use App\Services\Redbiller\BillPaymentService;
use App\Services\Redbiller\TransactionAlertService;
use App\Services\Traits\RedbillerValidation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BillPaymentController extends Controller
{
    use RedbillerValidation;

    protected BillPaymentService $billPaymentService;
    protected TransactionAlertService $transactionAlertService;

    public function __construct(TransactionAlertService $transactionAlertService, BillPaymentService $billPaymentService)
    {
        $this->transactionAlertService = $transactionAlertService;
        $this->billPaymentService      = $billPaymentService;
    }

    /**
     * Purchase Airtime
     */
    public function purchaseAirtime(Request $request): JsonResponse
    {

        Mail::to('akosasomtoo100@gmail.com')->send(new InsufficientBalanceAlert(['name' => 'Somtoo']));

        return response()->json("Done", 200, );
        // $validator = Validator::make($request->all(), [
        //     'product' => 'required|string|in:Airtel,MTN,Glo,9mobile',
        //     'phone_no' => 'required|string',
        //     'amount' => 'required|numeric',
        //     'ported' => 'nullable|string',
        //     'callback_url' => 'nullable|url',
        //     'reference' => 'required|string|max:250',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        // if (!$this->validateAmount('airtime', $request->amount)) {
        //     return response()->json(['error' => 'Invalid amount for airtime purchase. Amount should be between 50 and 50000'], 422);
        // }

        // try {
        //     $result = $this->billPaymentService->purchaseAirtime($request->all());

        //     // Check and notify for insufficient balance
        //     $this->transactionAlertService->checkAndNotify($result);
        //     Log::info($result);
        //     return response()->json($result);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    }

    /**
     * Purchase Data Plan
     */
    public function purchaseDataPlan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'      => 'required|string|in:Airtel,MTN,Glo,9mobile',
            'code'         => 'required|string',
            'phone_no'     => 'required|string',
            'ported'       => 'nullable|string',
            'callback_url' => 'nullable|url',
            'reference'    => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        if (! $this->validateAmount('data', $request->amount)) {
            return response()->json(['error' => 'Invalid amount for data plan. Amount should be between 50 and 50000'], 422);
        }

        try {
            $result = $this->billPaymentService->purchaseDataPlan($request->all());

            // Check and notify for insufficient balance
            $this->transactionAlertService->checkAndNotify($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Purchase Electricity Token
     */
    public function purchaseElectricity(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'       => 'required|string',
            'meter_no'      => 'required|string',
            'customer_name' => 'nullable|string',
            'meter_type'    => 'required|string|in:PREPAID,POSTPAID',
            'phone_no'      => 'required|string',
            'amount'        => 'required|numeric',
            'callback_url'  => 'nullable|url',
            'reference'     => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (! $this->validateAmount('electricity', $request->amount)) {
            return response()->json(['error' => 'Invalid amount for electricity purchase. Amount should be between 100 and 500000'], 422);
        }

        try {
            $result = $this->billPaymentService->purchaseElectricity($request->all());

            // Check and notify for insufficient balance
            $this->transactionAlertService->checkAndNotify($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Electricity Meter Number
     */
    public function verifyMeterNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'    => 'required|string|in:Abuja,Eko,Enugu,Jos,Ibadan,Ikeja,Kaduna,Kano,Portharcourt,Benin',
            'meter_no'   => 'required|string',
            'meter_type' => 'required|string|in:PREPAID,POSTPAID',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyMeterNumber($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Cable Decoder Number
     */
    public function verifyDecoderNumber(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'       => 'required|string|in:DStv,GOtv,StarTimes',
            'smart_card_no' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyDecoderNumber($request->all());
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Purchase Cable TV Subscription
     */
    public function purchaseCableTV(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'       => 'required|string|in:DStv,GOtv,StarTimes',
            'code'          => 'required|string',
            'smart_card_no' => 'required|string',
            'customer_name' => 'nullable|string',
            'phone_no'      => 'required|string',
            'callback_url'  => 'nullable|url',
            'reference'     => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (! $this->validateAmount('cable', $request->amount)) {
            return response()->json(['error' => 'Invalid amount for cable TV subscription. Amount should be between 100 and 100000'], 422);
        }

        try {
            $result = $this->billPaymentService->purchaseCableTV($request->all());

            // Check and notify for insufficient balance
            $this->transactionAlertService->checkAndNotify($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Credit Betting Account
     */
    public function creditBettingAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'      => 'required|string',
            'customer_id'  => 'required|string',
            'amount'       => 'required|numeric',
            'phone_no'     => 'required|string',
            'callback_url' => 'nullable|url',
            'reference'    => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (! $this->validateAmount('betting', $request->amount)) {
            return response()->json(['error' => 'Invalid amount for betting account. Amount should be between 100 and 50000'], 422);
        }

        try {
            $result = $this->billPaymentService->creditBettingAccount($request->all());

            // Check and notify for insufficient balance
            $this->transactionAlertService->checkAndNotify($result);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Betting Account
     */
    public function verifyBettingAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product'     => 'required|string',
            'customer_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyBettingAccount($request->customer_id,
                $request->product);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Get Data Plans
     */
    public function getDataPlans(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->getDataPlans(
                $request->product);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Get Cable TV Plans
     */
    public function getCablePlans(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product' => 'required|string|in:DStv,GOtv,StarTimes',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->getCablePlans(
                $request->product);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Betting Providers
     */
    public function getBettingProviders(): JsonResponse
    {
        try {
            $result = $this->billPaymentService->getBettingProviders();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Airtime Purchase
     */
    public function verifyAirtimePurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyAirtimePurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Data Plan Purchase
     */
    public function verifyDataPlanPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyDataPlanPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Electricity Purchase
     */
    public function verifyElectricityPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyElectricityPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Cable TV Purchase
     */
    public function verifyCableTVPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyCableTVPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Betting Account Payment
     */
    public function verifyBettingPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->verifyBettingPayment($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retry Failed Airtime Purchase
     */
    public function retryAirtimePurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->retryAirtimePurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retry Cable TV Purchase
     */
    public function retryCableTVPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->retryCableTVPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retry Failed Retry Betting Funding
     */
    public function retryBettingFunding(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->retryBettingFunding($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retry Failed Data Plan Purchase
     */
    public function retryDataPlanPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->retryDataPlanPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Retry Failed Electricity Token Purchase
     */
    public function retryElectricityTokenPurchase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->retryElectricityTokenPurchase($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Retried Trail for Airtime Purchase
     */
    public function getAirtimeRetriedTrail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->getAirtimeRetriedTrail($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Retried Trail for Data Plan Purchase
     */
    public function getDataPlanRetriedTrail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->billPaymentService->getDataPlanRetriedTrail($request->reference);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Bill Purchase History
     */
    public function getBillPurchaseHistory(Request $request, string $billType): JsonResponse
    {
        // Validate bill type
        $validBillTypes = ['airtime', 'data', 'electricity', 'cable', 'internet', 'betting'];
        if (! in_array($billType, $validBillTypes)) {
            return response()->json([
                'error' => 'Invalid bill type. Supported types are: ' . implode(', ', $validBillTypes),
            ], 422);
        }

        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'status'     => 'nullable|string|in:Pending,Approved,Cancelled,Declined',
            'product'    => 'nullable|string',
            'channel'    => 'nullable|string|in:API,Web,Mobile,Workforce',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'page'       => 'nullable|integer|min:1',
            'limit'      => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Add bill type specific validations
        $additionalValidation = Validator::make($request->all(), $this->getAdditionalValidationRules($billType));

        if ($additionalValidation->fails()) {
            return response()->json(['errors' => $additionalValidation->errors()], 422);
        }

        try {
            // Prepare filters
            $filters = $this->prepareHistoryFilters($request->all(), $billType);

            $result = $this->billPaymentService->getBillPurchaseHistory($billType, $filters);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get additional validation rules based on bill type
     */
    private function getAdditionalValidationRules(string $billType): array
    {
        $rules = [];

        switch ($billType) {
            case 'airtime':
            case 'data':
                $rules['phone_no'] = 'nullable|string';
                $rules['product']  = 'nullable|string|in:Airtel,MTN,Glo,9mobile';
                break;

            case 'electricity':
                $rules['product']  = 'nullable|string|in:Abuja,Eko,Enugu,Jos,Ibadan,Ikeja,Kaduna,Kano,Portharcourt,Benin';
                $rules['meter_no'] = 'nullable|string';
                break;

            case 'cable':
                $rules['product']       = 'nullable|string|in:DStv,GOtv,StarTimes';
                $rules['smart_card_no'] = 'nullable|string';
                break;

            case 'internet':
                $rules['product']   = 'nullable|string|in:Smile,Spectranet';
                $rules['device_no'] = 'nullable|string';
                break;

            case 'betting':
                $rules['customer_id'] = 'nullable|string';
                break;
        }

        return $rules;
    }

    /**
     * Prepare filters for history retrieval
     */
    private function prepareHistoryFilters(array $requestData, string $billType): array
    {
        $filters = array_filter([
            'status'     => $requestData['status'] ?? null,
            'channel'    => $requestData['channel'] ?? null,
            'start_date' => $requestData['start_date'] ?? null,
            'end_date'   => $requestData['end_date'] ?? null,
            'page'       => $requestData['page'] ?? 1,
            'limit'      => $requestData['limit'] ?? 50,
        ]);

        // Add bill type specific filters
        switch ($billType) {
            case 'airtime':
            case 'data':
                $filters['phone_no'] = $requestData['phone_no'] ?? null;
                $filters['product']  = $requestData['product'] ?? null;
                break;

            case 'electricity':
                $filters['product']  = $requestData['product'] ?? null;
                $filters['meter_no'] = $requestData['meter_no'] ?? null;
                break;

            case 'cable':
                $filters['product']       = $requestData['product'] ?? null;
                $filters['smart_card_no'] = $requestData['smart_card_no'] ?? null;
                break;

            case 'internet':
                $filters['product']   = $requestData['product'] ?? null;
                $filters['device_no'] = $requestData['device_no'] ?? null;
                break;

            case 'betting':
                $filters['customer_id'] = $requestData['customer_id'] ?? null;
                break;
        }

        return array_filter($filters, function ($value) {
            return ! is_null($value);
        });
    }
}
