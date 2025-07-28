<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Package;
use App\Helpers\EncryptionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignalApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test packages
        Package::create([
            'name' => 'free_package',
            'cost' => 0,
            'currency' => 'USD',
            'tax_rate' => 0,
            'modules' => ['api_access']
        ]);

        Package::create([
            'name' => 'basic_package',
            'cost' => 29.99,
            'currency' => 'USD',
            'tax_rate' => 0.0825,
            'modules' => ['api_access', 'analytics']
        ]);
    }

    /** @test */
    public function it_processes_valid_signal_successfully()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "basic_package:{$timestamp->format('Y:m:d:H')}:{$host}";

        // Create existing tenant
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        $package = Package::where('name', 'basic_package')->first();
        $tenant->assignPackage($package);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'tenant_id',
                    'tenant_host',
                    'tenant_name',
                    'package_id',
                    'package_name',
                    'package_cost',
                    'package_currency',
                    'package_tax_rate',
                    'package_modules',
                    'signal_timestamp',
                    'processed_at',
                    'expires_at'
                ],
                'signature'
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'tenant_id' => $tenant->id,
                    'tenant_host' => $host,
                    'package_name' => 'basic_package'
                ]
            ]);
    }

    /** @test */
    public function it_creates_new_tenant_with_free_package_when_not_found()
    {
        // Arrange
        $host = 'newhost.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "free_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'tenant_host' => $host,
                    'package_name' => 'free_package'
                ]
            ]);

        // Verify tenant was created
        $this->assertDatabaseHas('tenants', [
            'host' => $host,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_encrypted_host_id()
    {
        // Arrange
        $invalidEncryptedHostId = 'invalid-hex-string';
        $hash = 'basic_package:2024:01-15:10:example.com';

        // Act
        $response = $this->postJson("/{$invalidEncryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }

    /** @test */
    public function it_returns_error_for_blocked_tenant()
    {
        // Arrange
        $host = 'blocked.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "basic_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        // Create blocked tenant
        Tenant::create([
            'name' => 'Blocked Tenant',
            'host' => $host,
            'status' => 'blocked'
        ]);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }

    /** @test */
    public function it_returns_error_for_expired_token()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $oldTimestamp = now()->subHours(2);
        $hash = "basic_package:2024:{$oldTimestamp->format('m-d')}:{$oldTimestamp->format('H')}:{$host}";

        // Create tenant
        Tenant::create([
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }

    /** @test */
    public function it_returns_error_for_invalid_hash_format()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $invalidHash = 'invalid:hash:format';

        // Create tenant
        Tenant::create([
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $invalidHash
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }

    /** @test */
    public function it_returns_error_for_missing_hash_parameter()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", []);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }

    /** @test */
    public function it_returns_error_for_nonexistent_package()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "nonexistent_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        // Create tenant
        Tenant::create([
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        // Act
        $response = $this->postJson("/{$encryptedHostId}/signal", [
            'hash' => $hash
        ]);

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Invalid Client – contact sales@avinertech.com'
            ]);
    }
} 