<?php

// Register the Composer autoloader...
require __DIR__.'/../../../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

// Ensure the Composer ClassLoader is loaded
if (!isset($loader)) {
    $loader = new ClassLoader();
}

// Manually register namespace-to-directory mappings
$loader->addPsr4('App\\', __DIR__ . '/app/');
$loader->addPsr4('Database\\Factories\\', __DIR__ . '/database/factories/');
$loader->addPsr4('Database\\Seeders\\', __DIR__ . '/database/seeders/');
$loader->addPsr4('CustomNamespace\\', __DIR__ . '/CustomNamespace/');

// Register the autoloader
$loader->register();
