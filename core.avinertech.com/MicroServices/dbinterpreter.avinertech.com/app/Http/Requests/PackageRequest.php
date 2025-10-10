<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $packageId = $this->route('id') ?? $this->route('package');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z][a-z0-9_]*$/', // Snake case validation
                Rule::unique('packages', 'name')->ignore($packageId),
            ],
            'cost' => 'required|numeric|min:0|max:999999.99',
            'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/', // ISO currency codes
            'tax_rate' => 'required|numeric|min:0|max:1', // 0 to 100% as decimal
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required',
            'name.unique' => 'This package name is already taken',
            'name.regex' => 'Package name must be in snake_case format (lowercase, underscores only)',
            'cost.required' => 'Cost is required',
            'cost.numeric' => 'Cost must be a number',
            'cost.min' => 'Cost cannot be negative',
            'cost.max' => 'Cost is too large',
            'currency.required' => 'Currency is required',
            'currency.size' => 'Currency must be exactly 3 characters',
            'currency.regex' => 'Currency must be a valid ISO code (e.g., USD, EUR)',
            'tax_rate.required' => 'Tax rate is required',
            'tax_rate.numeric' => 'Tax rate must be a number',
            'tax_rate.min' => 'Tax rate cannot be negative',
            'tax_rate.max' => 'Tax rate cannot exceed 100%',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert name to snake_case if not already
        if ($this->has('name')) {
            $this->merge([
                'name' => strtolower(str_replace(' ', '_', $this->name))
            ]);
        }

        // Ensure currency is uppercase
        if ($this->has('currency')) {
            $this->merge([
                'currency' => strtoupper($this->currency)
            ]);
        }
    }
} 