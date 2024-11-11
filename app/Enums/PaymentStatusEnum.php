<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    use BaseEnum;

    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
}
