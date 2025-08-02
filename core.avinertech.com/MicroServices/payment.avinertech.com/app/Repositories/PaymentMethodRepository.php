<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    /**
     * Get all active payment methods ordered by fallback sequence.
     */
    public function getActiveMethodsOrdered(): Collection
    {
        return PaymentMethod::active()->ordered()->get();
    }

    /**
     * Find a payment method by name.
     */
    public function findByName(string $name): ?PaymentMethod
    {
        return PaymentMethod::where('name', $name)->first();
    }

    /**
     * Create a new payment method.
     */
    public function create(array $data): PaymentMethod
    {
        return PaymentMethod::create($data);
    }

    /**
     * Update a payment method.
     */
    public function update(PaymentMethod $method, array $data): PaymentMethod
    {
        $method->update($data);
        return $method->fresh();
    }

    /**
     * Delete a payment method.
     */
    public function delete(PaymentMethod $method): bool
    {
        return $method->delete();
    }

    /**
     * Toggle payment method active status.
     */
    public function toggleActive(PaymentMethod $method): PaymentMethod
    {
        $method->update(['is_active' => !$method->is_active]);
        return $method->fresh();
    }

    /**
     * Update payment method order.
     */
    public function updateOrder(PaymentMethod $method, int $order): PaymentMethod
    {
        $method->update(['order' => $order]);
        return $method->fresh();
    }
} 