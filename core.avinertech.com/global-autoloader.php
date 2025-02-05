<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

global $base, $loaderBinding;

if (file_exists($maintenance = $base.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

if (!isset($loader)) {
    $loader = new ClassLoader();
}

$defaultLoaderBinding = [
    'App\\' => '/../app/',
    'Database\\Factories\\' => '/../database/factories/',
    'Database\\Seeders\\' => '/../database/seeders/'
];

$loaderBinding = array_merge($loaderBinding, $defaultLoaderBinding);
foreach ($loaderBinding as $class => $dir) $loader->addPsr4($class,  "$base$dir");
$loader->register();

// Bootstrap Laravel and handle the request...
(require_once $base.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
