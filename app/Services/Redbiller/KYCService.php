<?php
namespace App\Services\Redbiller;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KYCService
{
    protected string $baseUrl;
    protected string $privateKey;
    protected array $headers;

    public function __construct()
    {
        $this->baseUrl    = config('services.redbiller.base_url');
        $this->privateKey = config('services.redbiller.private_key');
        $this->headers    = [
            'Private-Key'  => $this->privateKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Lookup BVN using account details
     */
    public function lookupBVN(string $accountNo, string $bankCode, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bvn/lookup', [
                    'account_no' => $accountNo,
                    'bank_code'  => $bankCode,
                    'reference'  => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('BVN Lookup Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify BVN with enhanced details (BVN 2.0)
     */
    public function verifyBVN2(string $bvn, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bvn/verify.2.0', [
                    'bvn'       => $bvn,
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('BVN 2.0 Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Verify BVN with enhanced details (BVN 2.0)
     */
    public function verifyBVN(string $bvn, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bvn/verify.1.0', [
                    'bvn'       => $bvn,
                    'reference' => $reference,
                ]);
            Log::info('BVN 1.0 Verification Successful: ' . $response);

            return $response->json();
        } catch (Exception $e) {
            Log::error('BVN 1.0 Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify BVN with advanced validation (BVN 3.0)
     */
    public function verifyBVN3(string $bvn, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bvn/verify.3.0', [
                    'bvn'       => $bvn,
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('BVN 3.0 Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Bank account lookup for additional information
     */
    public function lookupBankAccount(string $accountNo, string $bankCode, string $reference): array
    {

        $originalBaseUrl = $this->baseUrl;                                       // Store the original base URL
        $this->baseUrl   = preg_replace('/\/\d+\.\d+/', '/1.2', $this->baseUrl); // Change to version 1.2
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bank-account/lookup', [
                    'account_no' => $accountNo,
                    'bank_code'  => $bankCode,
                    'reference'  => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Account Lookup Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Find bank accounts by account number
     */
    public function findBankAccount(string $accountNo, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bank-account/find', [
                    'account_no' => $accountNo,
                    'reference'  => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Account Find Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify phone number with BVN (Advanced - Version 3.0)
     */
    public function verifyPhoneNumber3(string $phoneNo, string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/phone-number/verify.3.0', [
                    'phone_no'  => $phoneNo,
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Phone Number Verification 3.0 Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify National ID Number (NIN)
     */
    public function verifyNIN(string $nin, string $reference): array
    {

        $originalBaseUrl = $this->baseUrl;                                       // Store the original base URL
        $this->baseUrl   = preg_replace('/\/\d+\.\d+/', '/1.2', $this->baseUrl); // Change to version 1.2

        Log::alert("message: " . $this->baseUrl . '/kyc/nin/verify.1.0');
        try {
            $response = Http::withHeaders($this->headers)
            // ->post('https://api.mockfly.dev/mocks/0e8bc8ec-7230-450f-a05e-04610e68eab7/user/kyc/nin/validate', [
                ->post($this->baseUrl . '/kyc/nin/verify.1.0', [
                    'nin'       => $nin,
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('NIN Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify Voter's Card
     */
    public function verifyVotersCard(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/vin/verify', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Voter\'s Card Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify International Passport
     */
    public function verifyPassport(array $data): array
    {
        $originalBaseUrl = $this->baseUrl;                                       // Store the original base URL
        $this->baseUrl   = preg_replace('/\/\d+\.\d+/', '/1.2', $this->baseUrl); // Change to version 1.2

        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/passport/verify.2.0', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Passport Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get Bank Account Tier
     */
    public function getBankAccountTier(string $accountNo, string $bankCode): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bank-account/get-tier', [
                    'account_no' => $accountNo,
                    'bank_code'  => $bankCode,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Account Tier Check Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate Bank Account Tier
     */
    public function validateBankAccountTier(string $accountNo, string $bankCode, float $amount): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bank-account/validate-tier', [
                    'account_no' => $accountNo,
                    'bank_code'  => $bankCode,
                    'amount'     => $amount,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Account Tier Validation Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
