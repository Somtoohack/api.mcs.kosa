<?php
namespace App\Services\Redbiller;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RedbillerService
{
    protected string $baseUrl;
    protected string $privateKey;
    protected string $publicKey;
    protected array $headers;

    public function __construct()
    {
        $this->baseUrl    = config('services.redbiller.base_url');
        $this->privateKey = config('services.redbiller.private_key');
        $this->publicKey  = config('services.redbiller.public_key');
        $this->headers    = [
            'Private-Key'  => $this->privateKey,
            'Content-Type' => 'application/json',
        ];
    }

    // Start 3D Authentication Methods--------------------------------------------
    /**
     * Create 3D Authentication file
     */
    public function create3DAuthFile(string $reference): bool
    {
        try {
            $basePath = public_path('redbiller');
            $hookPath = $basePath . '/' . config('services.redbiller.auth_hook');

            if (! file_exists($basePath)) {
                mkdir($basePath, 0755, true);
            }

            if (! file_exists($hookPath)) {
                mkdir($hookPath, 0755, true);
            }

            $filePath = $hookPath . '/' . $reference;
            return file_put_contents($filePath, $reference) !== false;
        } catch (Exception $e) {
            Log::error('3D Auth File Creation Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify 3D Authentication setup
     */
    public function verify3DAuthSetup(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/3d-authentication/setup/verify', [
                    'pointer' => '10101010',
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('3D Auth Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End 3D Authentication Methods

    // Start Bank Transfer Methods--------------------------------------------
    /**
     * Initiate bank transfer
     */
    public function initiateBankTransfer(array $data): array
    {
        try {
            if (! $this->create3DAuthFile($data['reference'])) {
                throw new Exception('Failed to create 3D authentication file');
            }

            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/payout/bank-transfer/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Transfer Initiation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify bank transfer status
     */
    public function verifyBankTransfer(string $reference): array
    {
        try {
            Log::info("Verifying Bank Transfer.....");
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/payout/bank-transfer/status', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Transfer Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Bank Transfer Methods

    // Start Bank Methods--------------------------------------------
    /**
     * Get bank list
     */
    public function getBankList(string $type = 'Commercial'): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/payout/bank-transfer/banks/list', [
                    'country_code'  => 'NG',
                    'currency_code' => 'NGN',
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank List Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify bank account
     */
    public function verifyBankAccount(string $accountNo, string $bankCode): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/kyc/bank-account/verify', [
                    'account_no' => $accountNo,
                    'bank_code'  => $bankCode,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Bank Account Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // End Bank Methods

    // Start Balance Methods--------------------------------------------
    /**
     * Get account balance
     */
    public function getBalance(): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/get/balance');

            return $response->json();
        } catch (Exception $e) {
            Log::error('Balance Check Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Balance Methods

    // Start Payment Sub-Account Methods--------------------------------------------
    /**
     * Create Payment Sub-Account
     */
    public function createPaymentSubAccount(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/collections/PSA/create', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Payment Sub-Account Creation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get Payment Sub-Account Balance
     */
    public function getPSABalance(string $reference): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/collections/PSA/get-balance', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('PSA Balance Check Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Request PSA Debit
     */
    public function requestPSADebit(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/collections/PSA/debit/request', $data);

            return $response->json();
        } catch (Exception $e) {
            Log::error('PSA Debit Request Failed: ' . $e->getMessage());
            throw $e;
        }
    }
    // End Payment Sub-Account Methods

    // Start Transaction Methods--------------------------------------------
    /**
     * Get Transaction History
     */
    public function getTransactionHistory(array $filters = []): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . '/get/statement', $filters);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Transaction History Retrieval Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyDeposit(string $reference): array
    {
        try {

            Log::info("Verifying Deposit.....");
            $response = Http::withHeaders($this->headers)
                ->post($this->baseUrl . '/collections/PSA/payments/verify', [
                    'reference' => $reference,
                ]);

            return $response->json();
        } catch (Exception $e) {
            Log::error('Deposit Verification Failed: ' . $e->getMessage());
            throw $e;
        }
    }

}
