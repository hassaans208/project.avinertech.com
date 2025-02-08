<?php

use Symfony\Component\Console\Input\ArgvInput;
require __DIR__.'/../vendor/autoload.php';
use Composer\Autoload\ClassLoader;

global $base, $system, $loaderBinding;

define('LARAVEL_START', microtime(true));

if (!isset($loader)) {
    $loader = new ClassLoader();
}

$defaultLoaderBinding = [
    'App\\' => '/app/',
    'Database\\Factories\\' => '/database/factories/',
    'Database\\Seeders\\' => '/database/seeders/'
];

$loaderBinding = array_merge($loaderBinding, $defaultLoaderBinding);
foreach ($loaderBinding as $class => $dir) $loader->addPsr4($class, "$base$dir");
$loader->addPsr4('App\\Models\\',  __DIR__."/Services/migrator.avinertech.com/app/Models");
$loader->register();

$arg = new ArgvInput;
$arg = $arg->getFirstArgument();
$system = "";

if((str_contains($arg, 'migrate') || str_contains($arg, 'model')) && !str_contains($base, 'migrator.avinertech.com')) $system = "/Services/migrator.avinertech.com";

$app = (require_once "$base$system/bootstrap/app.php");
$status = $app->handleCommand(new ArgvInput);

exit($status);
