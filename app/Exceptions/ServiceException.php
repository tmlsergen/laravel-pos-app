<?php

namespace App\Exceptions;

class ServiceException extends \Exception implements \Throwable
{
    public function __construct(string $message = 'Error on service', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
