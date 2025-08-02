<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by signature verification
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tenant_id' => 'required|integer|exists:tenants,id',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'currency' => 'sometimes|string|size:3|in:USD,EUR,GBP,CAD,AUD',
            'package_cost' => 'sometimes|numeric|min:0|max:999999.99',
            'transaction_id' => 'sometimes|string|max:255|unique:payment_transactions,transaction_id',
            'metadata' => 'sometimes|array',
            'metadata.*' => 'string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tenant_id.required' => 'Tenant ID is required',
            'tenant_id.exists' => 'Invalid tenant ID',
            'amount.required' => 'Payment amount is required',
            'amount.min' => 'Payment amount must be at least $0.01',
            'amount.max' => 'Payment amount cannot exceed $999,999.99',
            'currency.size' => 'Currency must be a 3-letter code',
            'currency.in' => 'Unsupported currency',
            'transaction_id.unique' => 'Transaction ID already exists',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional validation logic can be added here
            $data = $this->validated();
            
            // Validate that package_cost doesn't exceed amount if both are provided
            if (isset($data['package_cost']) && isset($data['amount'])) {
                if ($data['package_cost'] > $data['amount']) {
                    $validator->errors()->add('package_cost', 'Package cost cannot exceed payment amount');
                }
            }
        });
    }
} 