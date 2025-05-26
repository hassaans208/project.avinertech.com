<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;

class ConfigurationController extends Controller
{
    protected $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * Get configurations by type
     *
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigurations(string $type)
    {
        try {
            $configurations = $this->configurationService->getConfigurations($type);
            return response()->json([
                'status' => 'success',
                'data' => $configurations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store configuration
     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeConfiguration(Request $request, string $type)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'configurations' => 'required|array',
                'configurations.*.name' => 'required|string|max:255',
                'configurations.*.value' => 'required|string',
                'configurations.*.host' => 'nullable|string|max:1000',
                'configurations.*.group' => 'nullable|string|max:1000',
                'configurations.*.type' => 'nullable|string|max:1000',
                // 'configurations.*.description' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Store configurations
            $storedConfigs = $this->configurationService->storeConfiguration($type, $request->configurations);

            return response()->json([
                'status' => 'success',
                'message' => 'Configurations stored successfully',
                'data' => $storedConfigs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all configuration cache
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            dd('clearCache');
            // Clear all configuration cache
            Cache::flush();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Configuration cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test database connection with provided credentials
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testDatabaseConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'DATABASE_HOST' => 'required|string|max:255',
            'DATABASE_PORT' => 'required|integer|min:1|max:65535',
            'DATABASE_NAME' => 'required|string|max:255',
            'DATABASE_USERNAME' => 'required|string|max:255',
            'DATABASE_PASSWORD' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s",
                $request->DATABASE_HOST,
                $request->DATABASE_PORT,
                $request->DATABASE_NAME
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            $pdo = new PDO(
                $dsn,
                decrypt($request->DATABASE_USERNAME),
                decrypt($request->DATABASE_PASSWORD),
                $options
            );

            // Test the connection by executing a simple query
            $pdo->query('SELECT 1');

            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful',
                'data' => [
                    'server_info' => $pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
                    'connection_status' => $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
                ]
            ]);

        } catch (PDOException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }
} 