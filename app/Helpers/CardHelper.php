<?php

namespace App\Helpers;

class CardHelper
{
    public static function cardMask(string $cardNumber): string
    {
        return substr($cardNumber, 0, 4).' **** **** '.substr($cardNumber, -4);
    }
}
