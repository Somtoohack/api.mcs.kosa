<?php

use Illuminate\Support\Carbon;

function getTrx($length = 12)
{
    $characters = strtolower('ABCDEFGHJKMNOPQRSTUVWXYZ123456789');
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    $timestamp = Carbon::now()->format('YmdHis');
    $key = $randomString . $timestamp;

    $key = str_shuffle($key);
    return strrev($key);
}