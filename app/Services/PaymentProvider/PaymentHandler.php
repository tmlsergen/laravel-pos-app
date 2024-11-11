<?php

namespace App\Services\PaymentProvider;

use App\Enums\PaymentProviderEnum;
use App\Exceptions\ServiceException;
use App\Services\PaymentProvider\Service\GarantiService;
use App\Services\PaymentProvider\Service\PaymentProviderServiceInterface;

class PaymentHandler
{
    /**
     * @throws ServiceException
     */
    public static function make(string $provider): PaymentProviderServiceInterface
    {
        return match ($provider) {
            PaymentProviderEnum::GARANTI->value => app(GarantiService::class),
            default => throw new ServiceException('Payment provider not found'),
        };
    }
}
