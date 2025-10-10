<?php

namespace App\Exceptions;

class InvalidHashFormatException extends SignalException
{
    public function __construct(string $message = "Invalid hash format provided")
    {
        parent::__construct($message, 400);
    }
} 