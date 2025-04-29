<?php

// Get the tenant path from environment
$tenantPath = getenv('APP_PATH');

// Define the base path for the tenant's Laravel application
define('TENANT_BASE_PATH', $tenantPath);

// Load the tenant's autoloader
require TENANT_BASE_PATH . '/vendor/autoload.php';

// Load the tenant's environment file
$app = require_once TENANT_BASE_PATH . '/bootstrap/app.php';

// Set the tenant's storage path
$app->useStoragePath(TENANT_BASE_PATH . '/storage');

// Set the tenant's configuration path
$app->useConfigPath(TENANT_BASE_PATH . '/config');

// Set the tenant's database configuration
$app->config->set('database.connections.mysql.database', 'tenant_' . basename($tenantPath));

// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response); 