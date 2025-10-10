<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SignalService;
use App\Helpers\EncryptionHelper;
use App\Models\Tenant;
use App\Models\Package;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Exceptions\DecryptionException;
use App\Exceptions\TokenExpiredException;
use App\Exceptions\InvalidTenantException;
use App\Exceptions\InvalidHashFormatException;
use Mockery;
use Carbon\Carbon;

class SignalServiceTest extends TestCase
{
    private SignalService $signalService;
    private $tenantRepository;
    private $packageRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenantRepository = Mockery::mock(TenantRepositoryInterface::class);
        $this->packageRepository = Mockery::mock(PackageRepositoryInterface::class);
        
        $this->signalService = new SignalService(
            $this->tenantRepository,
            $this->packageRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_handles_successful_signal_processing()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "basic_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        $tenant = new Tenant([
            'id' => 1,
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        $package = new Package([
            'id' => 1,
            'name' => 'basic_package',
            'cost' => 29.99,
            'currency' => 'USD',
            'tax_rate' => 0.0825,
            'modules' => ['api_access', 'analytics']
        ]);

        $this->tenantRepository
            ->shouldReceive('findByHost')
            ->with($host)
            ->andReturn($tenant);

        $this->packageRepository
            ->shouldReceive('findByName')
            ->with('basic_package')
            ->andReturn($package);

        // Act
        $result = $this->signalService->handle($encryptedHostId, $hash);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertEquals($tenant->id, $result['data']['tenant_id']);
        $this->assertEquals($package->name, $result['data']['package_name']);
    }

    /** @test */
    public function it_throws_decryption_exception_for_invalid_encrypted_host_id()
    {
        // Arrange
        $invalidEncryptedHostId = 'invalid-hex-string';
        $hash = 'basic_package:2024:01-15:10:example.com';

        // Act & Assert
        $this->expectException(DecryptionException::class);
        $this->signalService->handle($invalidEncryptedHostId, $hash);
    }

    /** @test */
    public function it_throws_invalid_hash_format_exception_for_malformed_hash()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $invalidHash = 'invalid:hash:format';

        $tenant = new Tenant([
            'id' => 1,
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        $this->tenantRepository
            ->shouldReceive('findByHost')
            ->with($host)
            ->andReturn($tenant);

        // Act & Assert
        $this->expectException(InvalidHashFormatException::class);
        $this->signalService->handle($encryptedHostId, $invalidHash);
    }

    /** @test */
    public function it_throws_token_expired_exception_for_old_timestamp()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $oldTimestamp = now()->subHours(2);
        $hash = "basic_package:2024:{$oldTimestamp->format('m-d')}:{$oldTimestamp->format('H')}:{$host}";

        $tenant = new Tenant([
            'id' => 1,
            'name' => 'Test Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        $this->tenantRepository
            ->shouldReceive('findByHost')
            ->with($host)
            ->andReturn($tenant);

        // Act & Assert
        $this->expectException(TokenExpiredException::class);
        $this->signalService->handle($encryptedHostId, $hash);
    }

    /** @test */
    public function it_throws_invalid_tenant_exception_for_blocked_tenant()
    {
        // Arrange
        $host = 'example.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "basic_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        $blockedTenant = new Tenant([
            'id' => 1,
            'name' => 'Blocked Tenant',
            'host' => $host,
            'status' => 'blocked'
        ]);

        $this->tenantRepository
            ->shouldReceive('findByHost')
            ->with($host)
            ->andReturn($blockedTenant);

        // Act & Assert
        $this->expectException(InvalidTenantException::class);
        $this->signalService->handle($encryptedHostId, $hash);
    }

    /** @test */
    public function it_creates_new_tenant_when_not_found()
    {
        // Arrange
        $host = 'newhost.com';
        $encryptedHostId = EncryptionHelper::encryptAlphaNumeric($host);
        $timestamp = now();
        $hash = "free_package:2024:{$timestamp->format('m-d')}:{$timestamp->format('H')}:{$host}";

        $newTenant = new Tenant([
            'id' => 2,
            'name' => 'Newhost com Tenant',
            'host' => $host,
            'status' => 'active'
        ]);

        $freePackage = new Package([
            'id' => 1,
            'name' => 'free_package',
            'cost' => 0,
            'currency' => 'USD',
            'tax_rate' => 0,
            'modules' => ['api_access']
        ]);

        $this->tenantRepository
            ->shouldReceive('findByHost')
            ->with($host)
            ->andReturn(null);

        $this->tenantRepository
            ->shouldReceive('create')
            ->with([
                'name' => 'Newhost com Tenant',
                'host' => $host,
                'status' => 'active',
            ])
            ->andReturn($newTenant);

        $this->packageRepository
            ->shouldReceive('getFreePackage')
            ->andReturn($freePackage);

        $this->tenantRepository
            ->shouldReceive('assignPackage')
            ->with($newTenant, $freePackage);

        $this->packageRepository
            ->shouldReceive('findByName')
            ->with('free_package')
            ->andReturn($freePackage);

        // Act
        $result = $this->signalService->handle($encryptedHostId, $hash);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals($newTenant->id, $result['data']['tenant_id']);
    }
} 