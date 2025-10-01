<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterApplicationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test successful application registration
     */
    public function test_can_register_application_successfully()
    {
        $requestData = [
            // Package information
            'package_name' => 'Professional Package',
            'package_price_per_month' => 99.99,
            'total_price' => 99.99,
            
            // User/Company details
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => '123 Test Street, Test City, TC 12345',
            'host' => 'testcompany',
            'username' => 'testuser',
            'phone' => '+1234567890',
            
            // Database configuration
            'database_name' => 'test_db',
            'database_user' => 'test_user',
            'database_password' => 'test_password',
        ];

        $response = $this->postJson('/api/register-application', $requestData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'user_type',
                            'is_active',
                        ],
                        'tenant' => [
                            'id',
                            'name',
                            'host',
                            'status',
                        ],
                        'package' => [
                            'id',
                            'name',
                            'cost',
                            'currency',
                            'formatted_cost',
                        ],
                        'api_token',
                        'database_config' => [
                            'database_name',
                            'database_user',
                            'database_password',
                        ],
                    ],
                ]);

        // Verify data was stored correctly
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'testuser',
            'user_type' => 'TENANT_ADMIN',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Company',
            'host' => 'testcompany.avinertech.com',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('packages', [
            'name' => 'professional_package',
            'cost' => 99.99,
            'currency' => 'USD',
        ]);
    }

    /**
     * Test validation errors for required fields
     */
    public function test_validation_errors_for_required_fields()
    {
        $response = $this->postJson('/api/register-application', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'package_name',
                    'package_price_per_month',
                    'total_price',
                    'company_name',
                    'email',
                    'password',
                    'address',
                    'host',
                    'username',
                    'phone',
                ]);
    }

    /**
     * Test email uniqueness validation
     */
    public function test_email_must_be_unique()
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $requestData = [
            'package_name' => 'Basic Package',
            'package_price_per_month' => 29.99,
            'total_price' => 29.99,
            'company_name' => 'Test Company',
            'email' => 'existing@example.com', // Duplicate email
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => '123 Test Street',
            'host' => 'testcompany',
            'username' => 'testuser',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/register-application', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test host uniqueness validation
     */
    public function test_host_must_be_unique()
    {
        // Create existing tenant
        Tenant::factory()->create(['host' => 'existing.avinertech.com']);

        $requestData = [
            'package_name' => 'Basic Package',
            'package_price_per_month' => 29.99,
            'total_price' => 29.99,
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'address' => '123 Test Street',
            'host' => 'existing', // This will become existing.avinertech.com
            'username' => 'testuser',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/register-application', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['host']);
    }

    /**
     * Test password confirmation validation
     */
    public function test_password_confirmation_must_match()
    {
        $requestData = [
            'package_name' => 'Basic Package',
            'package_price_per_month' => 29.99,
            'total_price' => 29.99,
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword', // Mismatch
            'address' => '123 Test Street',
            'host' => 'testcompany',
            'username' => 'testuser',
            'phone' => '+1234567890',
        ];

        $response = $this->postJson('/api/register-application', $requestData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration status endpoint
     */
    public function test_can_get_registration_status()
    {
        // Create user with tenant and package
        $user = User::factory()->create(['email' => 'status@example.com']);
        $tenant = Tenant::factory()->create(['name' => 'Status Company']);
        $package = Package::factory()->create(['name' => 'status_package']);

        $user->assignToTenant($tenant, 'admin');
        $tenant->assignPackage($package);

        $response = $this->getJson('/api/registration-status/status@example.com');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'user_type',
                            'is_active',
                        ],
                        'tenant' => [
                            'id',
                            'name',
                            'host',
                            'status',
                        ],
                        'package' => [
                            'id',
                            'name',
                            'cost',
                            'currency',
                            'formatted_cost',
                        ],
                    ],
                ]);
    }

    /**
     * Test registration status for non-existent user
     */
    public function test_registration_status_returns_404_for_non_existent_user()
    {
        $response = $this->getJson('/api/registration-status/nonexistent@example.com');

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found',
                ]);
    }
}
