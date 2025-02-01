<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}
// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

// Ensure the Composer ClassLoader is loaded
if (!isset($loader)) {
    $loader = new ClassLoader();
}

$tenant = $_GET['tenant'];
$dir = "Applications";

if($tenant == 'demo.avinertech.com') {
    $dir = "Stubs";
}

$absolutePath = __DIR__."/../";

if(is_dir(__dir__."/../$dir/$tenant")) {
    $absolutePath = __dir__."/../$dir/$tenant";
}

$loader->addPsr4('App\\', "$absolutePath/app/");
$loader->addPsr4('Database\\Factories\\', "$absolutePath/database/factories/");
$loader->addPsr4('Database\\Seeders\\', "$absolutePath/database/seeders/");
$loader->addPsr4('CustomNamespace\\', "$absolutePath/CustomNamespace/");

// Register the autoloader
$loader->register();

// Bootstrap Laravel and handle the request...
(require_once "$absolutePath/bootstrap/app.php")->handleRequest(Request::capture());
