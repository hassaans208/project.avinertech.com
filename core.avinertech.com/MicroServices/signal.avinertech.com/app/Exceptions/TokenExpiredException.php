<?php

namespace App\Exceptions;

class TokenExpiredException extends SignalException
{
    public function __construct(string $message = "Token has expired")
    {
        parent::__construct($message, 400);
    }
} 