<?php

namespace App\Services\Redbiller;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillPaymentService
{
    protected string $baseUrl;
    protected array $headers;

    public function __construct()
    {
        $this->baseUrl = config('services.redbiller.base_url');
        $this->headers = [
            'Private-Key' => config('services.redbiller.private_key'),
            'Content-Type' => 'application/json',
        ];
    }

    // Start Airtime Methods--------------------------------------------
    public function purchaseAirtime(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/airtime/purchase/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Airtime Purchase Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyAirtimePurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/airtime/purchase/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Airtime Purchase Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function retryAirtimePurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/airtime/purchase/retry', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Airtime Purchase Retry Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAirtimeRetriedTrail(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/bills/airtime/purchase/get-retried-trail', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Airtime Retried Trail Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Airtime Methods

    // Start Data Plan Methods--------------------------------------------
    public function getDataPlans(string $product): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/data/plans/list', [
                    'product' => $product,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Data Plans Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function purchaseDataPlan(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/data/plans/purchase/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Data Plan Purchase Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyDataPlanPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/data/plans/purchase/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Data Plan Purchase Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function retryDataPlanPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/data/plans/purchase/retry', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Data Plan Purchase Retry Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getDataPlanRetriedTrail(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/bills/data/plans/purchase/get-retried-trail', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Data Plan Retried Trail Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Data Plan Methods

    // Start Electricity Methods--------------------------------------------
    public function purchaseElectricity(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/disco/purchase/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Electricity Purchase Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyElectricityPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/disco/purchase/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Electricity Purchase Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function retryElectricityTokenPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/disco/purchase/retry', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Electricity Token Purchase Retry Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyMeterNumber(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/disco/meter/verify', [
                    'product' => $data['product'], // e.g., Abuja, Eko, Enugu, etc.
                    'meter_no' => $data['meter_no'],
                    'meter_type' => $data['meter_type'], // PREPAID or POSTPAID
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Meter Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Electricity Methods

    // Start Cable TV Methods--------------------------------------------
    public function purchaseCableTV(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/cable/plans/purchase/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Cable TV Purchase Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyCableTVPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/cable/plans/purchase/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Cable TV Purchase Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function retryCableTVPurchase(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/cable/plans/purchase/retry', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Cable TV Purchase Retry Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCablePlans(string $product): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/cable/plans/list', [
                    'product' => $product,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Cable Plans Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyDecoderNumber(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/cable/decoder/verify', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Decoder Number Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Cable TV Methods

    // Start Betting Methods

    public function creditBettingAccount(array $data): array
    {
        $originalBaseUrl = $this->baseUrl; // Store the original base URL
        $this->baseUrl = preg_replace('/\/\d+\.\d+/', '/1.5', $this->baseUrl); // Change to version 1.5

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/betting/account/payment/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Betting Account Credit Failed: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->baseUrl = $originalBaseUrl; // Restore the original base URL
        }
    }

    public function getBettingProviders(): array
    {
        $originalBaseUrl = $this->baseUrl; // Store the original base URL
        $this->baseUrl = preg_replace('/\/\d+\.\d+/', '/1.5', $this->baseUrl); // Change to version 1.5

        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/bills/betting/providers/list');

            return $response->json();
        } catch (Exception $e) {
            Log::error('Betting Providers Retrieval Failed: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->baseUrl = $originalBaseUrl; // Restore the original base URL
        }
    }

    public function verifyBettingPayment(string $reference): array
    {
        $originalBaseUrl = $this->baseUrl; // Store the original base URL
        $this->baseUrl = preg_replace('/\/\d+\.\d+/', '/1.4', $this->baseUrl); // Change to version 1.4

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/betting/account/payment/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Betting Payment Verification Failed: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->baseUrl = $originalBaseUrl; // Restore the original base URL
        }
    }

    public function verifyBettingAccount(string $customerId, string $product): array
    {
        $originalBaseUrl = $this->baseUrl; // Store the original base URL
        $this->baseUrl = preg_replace('/\/\d+\.\d+/', '/1.4', $this->baseUrl); // Change to version 1.4
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/betting/account/verify', [
                    'customer_id' => $customerId,
                    'product' => $product,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Betting Account Verification Failed: ' . $e->getMessage());
            throw $e;
        } finally {
            $this->baseUrl = $originalBaseUrl; // Restore the original base URL
        }
    }

    public function retryBettingFunding(string $reference): array
    {
        $originalBaseUrl = $this->baseUrl; // Store the original base URL
        $this->baseUrl = preg_replace('/\/\d+\.\d+/', '/1.4', $this->baseUrl); // Change to version 1.2

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/bills/betting/account/payment/retry', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bet Wallet Funding Retry Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // End Betting Methods

    /**
     * Get Bill Purchase History
     */
    public function getBillPurchaseHistory(string $billType, array $filters = []): array
    {
        try {
            $endpoints = [
                'airtime' => '/bills/airtime/purchase/list',
                'data' => '/bills/data/plans/purchase/list',
                'electricity' => '/bills/disco/purchase/list',
                'cable' => '/bills/cable/plans/purchase/list',
                'internet' => '/bills/internet/plans/purchase/list',
                'betting' => '/bills/betting/account/payment/list',
            ];

            if (!isset($endpoints[$billType])) {
                throw new Exception('Invalid bill type specified');
            }

            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . $endpoints[$billType], $filters);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bill Purchase History Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Transaction Methods

}