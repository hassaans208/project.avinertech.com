<?php

namespace App\Contracts;

use App\Models\PaymentTransaction;

interface PaymentInterface
{
    /**
     * Process a payment transaction.
     */
    public function processPayment(array $paymentData): array;

    /**
     * Verify a payment transaction.
     */
    public function verifyPayment(string $transactionId): array;

    /**
     * Refund a payment transaction.
     */
    public function refundPayment(PaymentTransaction $transaction, float $amount = null): array;

    /**
     * Handle webhook notifications.
     */
    public function handleWebhook(array $webhookData): array;

    /**
     * Get payment method configuration.
     */
    public function getConfig(): array;

    /**
     * Validate payment data before processing.
     */
    public function validatePaymentData(array $data): bool;
} 