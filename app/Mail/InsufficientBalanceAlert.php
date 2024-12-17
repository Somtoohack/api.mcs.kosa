<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InsufficientBalanceAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function build()
    {
        return $this->from('urgent-action@mcs.vobs.com', env('APP_NAME', 'VOBS'))
            ->subject('😴😑 Urgent Redbiller Provider Insufficient Balance Alert')
            ->view('mails.insufficient_balance')
            ->with(['result' => $this->result]);
    }
}