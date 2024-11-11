<?php

namespace App\Helpers;

class AppHelper
{
    public static function getClientIp(): string
    {
        return request()->ip();
    }
}
