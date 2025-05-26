<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
// Register the Composer autoloader...
require __DIR__.'/../../../../vendor/autoload.php';
require __DIR__.'/../../../../Module/GlobalSecurityPatch.php';

use Composer\Autoload\ClassLoader;

// Ensure the Composer ClassLoader is loaded
if (!isset($loader)) {
    $loader = new ClassLoader();
}

// new GlobalSecurityPatch('base');

// Manually register namespace-to-directory mappings
$loader->addPsr4('App\\', __DIR__ . '/../app/');
$loader->addPsr4('Database\\Factories\\', __DIR__ . '/../database/factories/');
$loader->addPsr4('Database\\Seeders\\', __DIR__ . '/../database/seeders/');
// $loader->addPsr4('CustomNamespace\\', __DIR__ . '/../CustomNamespace/');

// Register the autoloader
$loader->register();

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
