<?php
namespace App\Services\Redbiller;

use App\Mail\InsufficientBalanceAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TransactionAlertService
{
    public function checkAndNotify($result)
    {
        // Check for insufficient balance response
        if (
            isset($result['response']) && $result['response'] == 400 &&
            isset($result['message']) && $result['message'] == "Insufficient balance." &&
            isset($result['details']['error']['message']) &&
            $result['details']['error']['message'] == "Insufficient balance to process this transaction.") {

            // Send email alert
            try {
                Mail::to('nexdatech@gmail.com')
                    ->send(new InsufficientBalanceAlert($result));
            } catch (Throwable $th) {
                Log::error($th);
            }

        }
    }
}
