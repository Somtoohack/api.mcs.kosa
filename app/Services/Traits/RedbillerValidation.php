<?php

namespace App\Services\Traits;

trait RedbillerValidation
{
    /**
     * Validate amount for specific services
     */
    protected function validateAmount(string $service, float $amount): bool
    {
        $limits = [
            'paycode_atm' => ['min' => 1000, 'max' => 20000],
            'paycode_pos' => ['min' => 1000, 'max' => 10000],
            'airtime' => ['min' => 50, 'max' => 50000],
            'data' => ['min' => 50, 'max' => 50000],
            'electricity' => ['min' => 100, 'max' => 500000],
            'cable' => ['min' => 100, 'max' => 100000],
            'betting' => ['min' => 100, 'max' => 50000],
        ];

        if (!isset($limits[$service])) {
            return true;
        }

        return $amount >= $limits[$service]['min'] && $amount <= $limits[$service]['max'];
    }

    /**
     * Validate reference length
     */
    protected function validateReference(string $reference): bool
    {
        return strlen($reference) <= 250;
    }
}

trait RedbillerResponse
{
    /**
     * Format success response
     */
    protected function formatSuccessResponse(array $data, string $message = 'Successful'): array
    {
        return [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Format error response
     */
    protected function formatErrorResponse(string $message, int $code = 400): array
    {
        return [
            'status' => false,
            'message' => $message,
            'code' => $code,
        ];
    }
}