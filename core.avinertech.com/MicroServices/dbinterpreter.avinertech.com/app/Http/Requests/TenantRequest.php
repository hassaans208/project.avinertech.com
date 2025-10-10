<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
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
        $tenantId = $this->route('id') ?? $this->route('tenant');

        return [
            'name' => 'required|string|max:255',
            'host' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9.-]+$/', // Basic hostname validation
                Rule::unique('tenants', 'host')->ignore($tenantId),
            ],
            'status' => 'required|in:active,inactive,blocked',
            'block_reason' => 'nullable|string|max:500|required_if:status,blocked',
            'package_id' => 'nullable|exists:packages,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tenant name is required',
            'host.required' => 'Host is required',
            'host.unique' => 'This host is already taken',
            'host.regex' => 'Host must be a valid hostname format',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be active, inactive, or blocked',
            'block_reason.required_if' => 'Block reason is required when status is blocked',
            'block_reason.max' => 'Block reason cannot exceed 500 characters',
            'package_id.exists' => 'Selected package does not exist',
        ];
    }
} 