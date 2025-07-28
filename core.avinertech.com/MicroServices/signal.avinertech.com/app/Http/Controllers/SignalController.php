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
     * Handle incoming signal request.
     */
    public function handle(SignalRequest $request, string $encryptedHostId): JsonResponse
    {
        try {
            $response = $this->signalService->handle(
                $encryptedHostId,
                $request->validated()['hash']
            );

            return response()->json($response);

        } catch (SignalException $e) {
            return $e->render();
        }
    }
} 