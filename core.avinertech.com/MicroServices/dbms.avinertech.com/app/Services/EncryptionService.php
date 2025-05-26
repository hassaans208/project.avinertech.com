<?php

namespace App\Services;

use InvalidArgumentException;

/**
 * Custom exception for encryption/decryption errors
 */
class EncryptionException extends \Exception {}

class EncryptionService
{
    /**
     * The encryption cipher
     */
    private const CIPHER = 'aes-256-cbc';

    /**
     * Encrypt data using AES-256-CBC
     *
     * @param mixed $data
     * @param string $encryptionKey
     * @return string
     * @throws EncryptionException
     */
    public function encrypt($data, string $encryptionKey): string
    {
        try {
            // Validate key before proceeding
            if (!$this->validateKey($encryptionKey)) {
                throw new EncryptionException('Invalid encryption key format or length');
            }

            // Generate a random initialization vector
            $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
            if ($iv === false) {
                throw new EncryptionException('Failed to generate initialization vector');
            }
            
            // Convert data to JSON
            $jsonData = json_encode($data);
            if ($jsonData === false) {
                throw new EncryptionException('Failed to encode data to JSON: ' . json_last_error_msg());
            }

            // Generate a secure key from the provided encryption key
            $key = $this->deriveKey($encryptionKey);
            if (strlen($key) !== 32) {
                throw new EncryptionException('Key derivation failed: Invalid key length');
            }

            // Encrypt the data
            $encrypted = openssl_encrypt(
                $jsonData,
                self::CIPHER,
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($encrypted === false) {
                $error = openssl_error_string();
                throw new EncryptionException('Encryption failed: ' . ($error ?: 'Unknown OpenSSL error'));
            }

            // Combine IV and encrypted data and encode to base64
            $combined = $iv . $encrypted;
            $encoded = base64_encode($combined);
            
            if ($encoded === false) {
                throw new EncryptionException('Failed to base64 encode encrypted data');
            }

            return $encoded;

        } catch (EncryptionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EncryptionException('Encryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt data using AES-256-CBC
     *
     * @param string $encryptedData
     * @param string $encryptionKey
     * @return mixed
     * @throws EncryptionException
     */
    public function decrypt(string $encryptedData, string $encryptionKey)
    {
        try {
            // Validate key before proceeding
            if (!$this->validateKey($encryptionKey)) {
                throw new EncryptionException('Invalid encryption key format or length');
            }

            // Decode the base64 data
            $decoded = base64_decode($encryptedData, true);
            if ($decoded === false) {
                throw new EncryptionException('Invalid base64 data: Malformed input');
            }

            // Get IV length
            $ivLength = openssl_cipher_iv_length(self::CIPHER);
            if ($ivLength === false) {
                throw new EncryptionException('Failed to get cipher IV length');
            }

            // Check if the decoded data is long enough to contain IV
            if (strlen($decoded) <= $ivLength) {
                throw new EncryptionException('Invalid encrypted data: Data too short');
            }

            // Extract the IV and encrypted data
            $iv = substr($decoded, 0, $ivLength);
            $encrypted = substr($decoded, $ivLength);

            // Generate the key from the encryption key
            $key = $this->deriveKey($encryptionKey);
            if (strlen($key) !== 32) {
                throw new EncryptionException('Key derivation failed: Invalid key length');
            }

            // Decrypt the data
            $decrypted = openssl_decrypt(
                $encrypted,
                self::CIPHER,
                $key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                $error = openssl_error_string();
                throw new EncryptionException('Decryption failed: ' . ($error ?: 'Unknown OpenSSL error'));
            }

            // Decode the JSON data
            $data = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new EncryptionException('Failed to decode JSON data: ' . json_last_error_msg());
            }

            return $data;

        } catch (EncryptionException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EncryptionException('Decryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Derive a secure encryption key from the provided key
     *
     * @param string $key
     * @return string
     * @throws EncryptionException
     */
    private function deriveKey(string $key): string
    {
        try {
            // Use PBKDF2 to derive a secure key
            $salt = 'fixed_salt_for_key_derivation'; // In production, you might want to make this configurable
            $iterations = 10000;
            $keyLength = 32; // 256 bits for AES-256

            $derivedKey = hash_pbkdf2(
                'sha256',
                $key,
                $salt,
                $iterations,
                $keyLength,
                true
            );

            if ($derivedKey === false) {
                throw new EncryptionException('Key derivation failed: hash_pbkdf2 returned false');
            }

            return $derivedKey;
        } catch (\Exception $e) {
            throw new EncryptionException('Key derivation failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate the encryption key
     *
     * @param string $key
     * @return bool
     */
    public function validateKey(string $key): bool
    {
        try {
            // Minimum key length requirement
            if (strlen($key) < 32) {
                return false;
            }

            // Check if key contains only valid characters
            if (preg_match('/^[a-zA-Z0-9@#$%^&*()_+\-=\[\]{};\'"\\|,.<>\/?]+$/', $key) !== 1) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
} 