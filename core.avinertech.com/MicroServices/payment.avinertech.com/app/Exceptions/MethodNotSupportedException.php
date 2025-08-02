<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class MethodNotSupportedException extends Exception
{
    protected $message = 'Payment method not supported';
    protected $code = 400;

    public function __construct(string $method = null)
    {
        $message = $method 
            ? "Payment method '{$method}' is not supported or inactive"
            : 'Payment method not supported';
            
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Method not supported',
            'message' => $this->getMessage(),
            'code' => $this->getCode()
        ], 400);
    }
} 