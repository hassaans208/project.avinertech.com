<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class EncryptionHelper
{
    /**
     * Encrypt a string using custom AES-256-CBC encryption
     * Returns only alphanumeric characters (0-9, a-f) as hex
     */
    public static function encryptAlphaNumeric(string $string): string
    {
        $key = substr(hash('sha256', config('app.key')), 0, 32); // 256-bit key
        $iv = random_bytes(16); // 128-bit IV

        $encrypted = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return bin2hex($iv . $encrypted); // HEX = only 0–9 and a–f
    }

    /**
     * Decrypt a hex string using custom AES-256-CBC decryption
     */
    public static function decryptAlphaNumeric(string $hexString): string|false
    {
        $key = substr(hash('sha256', config('app.key')), 0, 32);
        
        // Validate hex string
        if (!ctype_xdigit($hexString)) {
            return false;
        }
        
        $data = hex2bin($hexString);
        
        // Ensure we have enough data for IV + encrypted content
        if (strlen($data) < 16) {
            return false;
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return $decrypted !== false ? $decrypted : false;
    }
} 