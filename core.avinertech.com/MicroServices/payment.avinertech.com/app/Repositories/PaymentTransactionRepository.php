<?php

namespace App\Repositories;

use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentTransactionRepository implements PaymentTransactionRepositoryInterface
{
    /**
     * Create a new payment transaction.
     */
    public function create(array $data): PaymentTransaction
    {
        return PaymentTransaction::create($data);
    }

    /**
     * Find transaction by ID.
     */
    public function findById(int $id): ?PaymentTransaction
    {
        return PaymentTransaction::with(['tenant', 'paymentMethod', 'fallbacks', 'logs'])
            ->find($id);
    }

    /**
     * Find transaction by external transaction ID.
     */
    public function findByTransactionId(string $transactionId): ?PaymentTransaction
    {
        return PaymentTransaction::with(['tenant', 'paymentMethod', 'fallbacks', 'logs'])
            ->where('transaction_id', $transactionId)
            ->first();
    }

    /**
     * Get transactions for a specific tenant.
     */
    public function getForTenant(int $tenantId, int $perPage = 15): LengthAwarePaginator
    {
        return PaymentTransaction::with(['paymentMethod', 'fallbacks'])
            ->forTenant($tenantId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get transactions by status.
     */
    public function getByStatus(string $status): Collection
    {
        return PaymentTransaction::with(['tenant', 'paymentMethod'])
            ->byStatus($status)
            ->get();
    }

    /**
     * Get pending transactions.
     */
    public function getPendingTransactions(): Collection
    {
        return PaymentTransaction::with(['tenant', 'paymentMethod', 'fallbacks'])
            ->whereIn('status', ['pending', 'processing'])
            ->get();
    }

    /**
     * Update transaction status.
     */
    public function updateStatus(PaymentTransaction $transaction, string $status): PaymentTransaction
    {
        $transaction->update([
            'status' => $status,
            'processed_at' => now()
        ]);
        
        return $transaction->fresh();
    }

    /**
     * Get transactions with fallbacks.
     */
    public function getWithFallbacks(): Collection
    {
        return PaymentTransaction::with(['fallbacks', 'paymentMethod'])
            ->whereHas('fallbacks')
            ->get();
    }

    /**
     * Get recent transactions.
     */
    public function getRecent(int $hours = 24): Collection
    {
        return PaymentTransaction::with(['tenant', 'paymentMethod'])
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 