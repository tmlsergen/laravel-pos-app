<?php

namespace App\Services\PaymentProvider\Client;

interface PaymentClientInterface
{
    public function make3dSecurePayment(array $data): array;

    public function makePayment(array $data): array;
}
