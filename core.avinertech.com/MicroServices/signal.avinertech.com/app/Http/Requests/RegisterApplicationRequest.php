<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterApplicationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Package information
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'package_name' => ['required', 'string', 'max:255'],
            'package_price_per_month' => ['required', 'numeric', 'min:0'],
            'total_price' => ['required', 'numeric', 'min:0'],
            
            // User/Company details
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'password_confirmation' => ['required', 'string', 'min:8'],
            'address' => ['required', 'string', 'max:500'],
            'host' => ['required', 'string', 'max:100', 'unique:tenants,host'],
            'username' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            
            // Database configuration (optional for now)
            'database_name' => ['nullable', 'string', 'max:100'],
            'database_user' => ['nullable', 'string', 'max:100'],
            'database_password' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'package_id.integer' => 'Package ID must be a valid integer.',
            'package_id.exists' => 'The selected package does not exist.',
            'package_name.required' => 'Package name is required.',
            'package_price_per_month.required' => 'Package price is required.',
            'package_price_per_month.numeric' => 'Package price must be a valid number.',
            'total_price.required' => 'Total price is required.',
            'total_price.numeric' => 'Total price must be a valid number.',
            
            'company_name.required' => 'Company name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            // 'password.confirmed' => 'Password confirmation does not match.',
            'address.required' => 'Address is required.',
            'host.required' => 'Host name is required.',
            'host.unique' => 'This host name is already taken.',
            'username.required' => 'Username is required.',
            'phone.required' => 'Phone number is required.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'package_id' => 'package ID',
            'package_name' => 'package name',
            'package_price_per_month' => 'package price',
            'total_price' => 'total price',
            'company_name' => 'company name',
            'password_confirmation' => 'password confirmation',
            'database_name' => 'database name',
            'database_user' => 'database user',
            'database_password' => 'database password',
        ];
    }
}
