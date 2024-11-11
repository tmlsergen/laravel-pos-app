<?php

namespace App\Services\PaymentProvider\Service;

interface PaymentProviderServiceInterface
{
    public function makePayment(array $data): array;

    public function make3dSecurePayment(array $data): array;

    public function callback(array $params): bool;
}
