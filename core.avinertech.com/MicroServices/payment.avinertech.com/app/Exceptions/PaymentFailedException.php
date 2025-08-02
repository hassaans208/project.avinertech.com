<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PaymentFailedException extends Exception
{
    protected $message = 'Payment processing failed';
    protected $code = 422;

    public function __construct(string $message = null, array $context = [])
    {
        $this->context = $context;
        parent::__construct($message ?? $this->message);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Payment failed',
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->context ?? []
        ], 422);
    }
} 