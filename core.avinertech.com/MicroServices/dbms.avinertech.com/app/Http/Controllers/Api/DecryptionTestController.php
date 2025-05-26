<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EncryptionService;
use App\Services\EncryptionException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DecryptionTestController extends Controller
{
    protected $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    /**
     * Test decryption of encrypted data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testDecryption(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'encrypted_data' => 'required|string',
            'encryption_key' => 'required|string|min:32',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log the attempt (without sensitive data)
            Log::info('Decryption attempt', [
                'key_length' => strlen($request->encryption_key),
                'data_length' => strlen($request->encrypted_data)
            ]);

            // Validate the encryption key
            if (!$this->encryptionService->validateKey($request->encryption_key)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid encryption key: Must be at least 32 characters and contain only valid characters'
                ], 400);
            }

            // Attempt to decrypt the data
            $decryptedData = $this->encryptionService->decrypt(
                $request->encrypted_data,
                $request->encryption_key
            );

            Log::info('Decryption successful', [
                'data_type' => gettype($decryptedData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data decrypted successfully',
                'decrypted_data' => $decryptedData
            ]);

        } catch (EncryptionException $e) {
            Log::error('Decryption failed', [
                'error' => $e->getMessage(),
                'key_length' => strlen($request->encryption_key),
                'data_length' => strlen($request->encrypted_data)
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => 'encryption_error',
                'hint' => $this->getDecryptionHint($e->getMessage())
            ], 400);
        } catch (\Exception $e) {
            Log::error('Unexpected error during decryption', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred during decryption',
                'error_type' => 'system_error'
            ], 500);
        }
    }

    /**
     * Test encryption of data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testEncryption(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required',
            'encryption_key' => 'required|string|min:32',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Log the attempt (without sensitive data)
            Log::info('Encryption attempt', [
                'key_length' => strlen($request->encryption_key),
                'data_type' => gettype($request->data)
            ]);

            // Validate the encryption key
            if (!$this->encryptionService->validateKey($request->encryption_key)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid encryption key: Must be at least 32 characters and contain only valid characters'
                ], 400);
            }

            // Attempt to encrypt the data
            $encryptedData = $this->encryptionService->encrypt(
                $request->data,
                $request->encryption_key
            );

            Log::info('Encryption successful', [
                'encrypted_length' => strlen($encryptedData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data encrypted successfully',
                'encrypted_data' => $encryptedData
            ]);

        } catch (EncryptionException $e) {
            Log::error('Encryption failed', [
                'error' => $e->getMessage(),
                'key_length' => strlen($request->encryption_key),
                'data_type' => gettype($request->data)
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_type' => 'encryption_error'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Unexpected error during encryption', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred during encryption',
                'error_type' => 'system_error'
            ], 500);
        }
    }

    /**
     * Get helpful hints for common decryption errors
     *
     * @param string $errorMessage
     * @return string|null
     */
    private function getDecryptionHint(string $errorMessage): ?string
    {
        $hints = [
            'Invalid base64 data' => 'The encrypted data appears to be malformed. Make sure it was properly base64 encoded.',
            'Invalid encrypted data: Data too short' => 'The encrypted data is too short. It may be corrupted or incomplete.',
            'Key derivation failed' => 'There was a problem with the encryption key. Make sure it meets the requirements.',
            'Decryption failed: Unknown OpenSSL error' => 'The decryption failed. This usually means either the encryption key is incorrect or the data is corrupted.',
            'Failed to decode JSON data' => 'The decrypted data could not be parsed as JSON. The data may be corrupted.'
        ];

        foreach ($hints as $error => $hint) {
            if (strpos($errorMessage, $error) !== false) {
                return $hint;
            }
        }

        return null;
    }
} 