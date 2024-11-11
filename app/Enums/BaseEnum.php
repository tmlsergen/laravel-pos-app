<?php

namespace App\Enums;

trait BaseEnum
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function toString(): string
    {
        return implode(',', self::values());
    }
}
