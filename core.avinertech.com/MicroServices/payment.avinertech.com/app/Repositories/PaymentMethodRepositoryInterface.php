<?php

namespace App\Repositories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

interface PaymentMethodRepositoryInterface
{
    /**
     * Get all active payment methods ordered by fallback sequence.
     */
    public function getActiveMethodsOrdered(): Collection;

    /**
     * Find a payment method by name.
     */
    public function findByName(string $name): ?PaymentMethod;

    /**
     * Create a new payment method.
     */
    public function create(array $data): PaymentMethod;

    /**
     * Update a payment method.
     */
    public function update(PaymentMethod $method, array $data): PaymentMethod;

    /**
     * Delete a payment method.
     */
    public function delete(PaymentMethod $method): bool;

    /**
     * Toggle payment method active status.
     */
    public function toggleActive(PaymentMethod $method): PaymentMethod;

    /**
     * Update payment method order.
     */
    public function updateOrder(PaymentMethod $method, int $order): PaymentMethod;
} 