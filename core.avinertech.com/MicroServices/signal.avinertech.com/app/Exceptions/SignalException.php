<?php

namespace App\Exceptions;

use Exception;

class SignalException extends Exception
{
    protected $statusCode = 400;

    public function __construct(string $message = "Invalid Client – contact sales@avinertech.com", int $statusCode = 400)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function render()
    {
        return response()->json([
            'error' => $this->getMessage()
        ], $this->statusCode);
    }
} 