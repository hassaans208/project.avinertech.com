<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class SignatureInvalidException extends Exception
{
    protected $message = 'Invalid signature provided';
    protected $code = 401;

    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Signature verification failed',
            'message' => $this->getMessage(),
            'code' => $this->getCode()
        ], 401);
    }
} 