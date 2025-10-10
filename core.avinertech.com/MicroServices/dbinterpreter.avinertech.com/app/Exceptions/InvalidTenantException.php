<?php

namespace App\Exceptions;

class InvalidTenantException extends SignalException
{
    public function __construct(string $message = "Tenant is not active or is blocked")
    {
        parent::__construct($message, 400);
    }
} 