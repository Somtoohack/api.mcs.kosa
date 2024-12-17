<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'reference',
        'amount',
        'settlement',
        'charge',
        'first_name',
        'surname',
        'phone_no',
        'email',
        'bvn',
        'account_name',
        'account_no',
        'bank_name',
        'payer_account_name',
        'payer_account_no',
        'payer_bank_name',
        'meta',
        'date',
        'status',
    ];
}