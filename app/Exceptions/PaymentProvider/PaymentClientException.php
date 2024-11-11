<?php

namespace App\Exceptions\PaymentProvider;

class PaymentClientException extends \Exception implements \Throwable
{
    public function __construct(string $message = 'Error on sending payment request', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
