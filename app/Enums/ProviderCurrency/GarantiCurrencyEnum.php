<?php

namespace App\Enums\ProviderCurrency;

use App\Enums\BaseEnum;

enum GarantiCurrencyEnum: int
{
    use BaseEnum;

    case TRY = 949;
    case USD = 840;
    case EUR = 978;
}
