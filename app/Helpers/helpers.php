<?php

use Illuminate\Support\Facades\Config;

if (! function_exists('appName')) {
    function appName(): string
    {
        return Config::get('app.name', 'TaskFlow');
    }
}

if (! function_exists('generateOtp')) {
    function generateOtp(int $length = 6): string
    {
        $min = 10 ** ($length - 1);
        $max = (10 ** $length) - 1;

        return (string) random_int($min, $max);
    }
}