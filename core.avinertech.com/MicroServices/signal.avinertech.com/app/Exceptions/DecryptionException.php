<?php

namespace App\Exceptions;

class DecryptionException extends SignalException
{
    public function __construct(string $message = "Invalid encrypted host ID")
    {
        parent::__construct($message, 400);
    }
} 