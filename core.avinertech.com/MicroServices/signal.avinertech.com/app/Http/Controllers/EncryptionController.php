<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\EncryptionHelper;

class EncryptionController extends Controller
{
    /**
     * Encrypt a value using custom AES-256-CBC encryption
     */
    public function encrypt(Request $request): JsonResponse
    {
        $request->validate([
            'value' => 'required|string|max:500'
        ]);
        
        try {
            $encrypted = EncryptionHelper::encryptAlphaNumeric($request->value);
            
            return response()->json([
                'success' => true,
                'original' => $request->value,
                'encrypted' => $encrypted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Encryption failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Decrypt a hex string using custom AES-256-CBC decryption
     */
    public function decrypt(Request $request): JsonResponse
    {
        $request->validate([
            'value' => 'required|string'
        ]);
        
        try {
            $decrypted = EncryptionHelper::decryptAlphaNumeric($request->value);
            
            if ($decrypted === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid encrypted value or decryption failed'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'encrypted' => $request->value,
                'decrypted' => $decrypted
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Decryption failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the encryption/decryption utility view
     */
    public function showUtility(Request $request)
    {
        $accessToken = $request->get('access_token', '');
        return view('encryptor-decryptor', compact('accessToken'));
    }
} 