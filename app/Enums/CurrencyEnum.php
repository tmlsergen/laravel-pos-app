<?php

namespace App\Enums;

enum CurrencyEnum: string
{
    use BaseEnum;

    case TRY = 'TRY';
    case USD = 'USD';
    case EUR = 'EUR';
}
