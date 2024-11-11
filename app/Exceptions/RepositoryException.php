<?php

namespace App\Exceptions;

class RepositoryException extends \Exception implements \Throwable
{
    public function __construct(string $message = 'Error on database', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
