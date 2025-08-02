<?php

namespace App\Repositories;

use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentTransactionRepositoryInterface
{
    /**
     * Create a new payment transaction.
     */
    public function create(array $data): PaymentTransaction;

    /**
     * Find transaction by ID.
     */
    public function findById(int $id): ?PaymentTransaction;

    /**
     * Find transaction by external transaction ID.
     */
    public function findByTransactionId(string $transactionId): ?PaymentTransaction;

    /**
     * Get transactions for a specific tenant.
     */
    public function getForTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get transactions by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get pending transactions.
     */
    public function getPendingTransactions(): Collection;

    /**
     * Update transaction status.
     */
    public function updateStatus(PaymentTransaction $transaction, string $status): PaymentTransaction;

    /**
     * Get transactions with fallbacks.
     */
    public function getWithFallbacks(): Collection;

    /**
     * Get recent transactions.
     */
    public function getRecent(int $hours = 24): Collection;
} 