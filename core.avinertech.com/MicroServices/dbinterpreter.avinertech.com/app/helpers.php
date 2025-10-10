<?php

use App\Helpers\EncryptionHelper;

if (!function_exists('encryptAlphaNumeric')) {
    /**
     * Encrypt a string using custom AES-256-CBC encryption
     * Returns only alphanumeric characters (0-9, a-f) as hex
     */
    function encryptAlphaNumeric(string $string): string
    {
        return EncryptionHelper::encryptAlphaNumeric($string);
    }
}

if (!function_exists('decryptAlphaNumeric')) {
    /**
     * Decrypt a hex string using custom AES-256-CBC decryption
     */
    function decryptAlphaNumeric(string $hexString): string|false
    {
        return EncryptionHelper::decryptAlphaNumeric($hexString);
    }
} 