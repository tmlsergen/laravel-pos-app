<?php

namespace App\Exceptions\PaymentProvider;

class PaymentServiceException extends \Exception implements \Throwable
{
    public function __construct(string $message = 'Error on Payment Service', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
