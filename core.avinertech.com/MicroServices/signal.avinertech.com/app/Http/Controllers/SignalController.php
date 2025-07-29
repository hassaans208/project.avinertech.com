<?php

namespace App\Http\Controllers;

use App\Services\SignalService;
use App\Http\Requests\SignalRequest;
use App\Exceptions\SignalException;
use Illuminate\Http\JsonResponse;

class SignalController extends Controller
{
    public function __construct(
        private SignalService $signalService
    ) {}

    /**
     * Handle signal processing with authentication
     */
    public function handle(SignalRequest $request, string $encryptedHostId): JsonResponse
    {
        try {
            $user = $request->user(); // Get authenticated user from middleware
            
            $result = $this->signalService->handle(
                $encryptedHostId,
                $request->input('hash'),
                $user
            );

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Network Error – contact sales@avinertech.com'
            ], 400);
        }
    }
} 