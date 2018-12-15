<?php

use Symfony\Component\Console\Application;

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ );
}

require APP_ROOT . '/vendor/autoload.php';
$container = require APP_ROOT . '/src/init.php';

$application = new Application();

array_map(function ($cmd) use ($application) {
    $application->add(new $cmd);
}, $container['config']['commands']);

try{
    $application->run();
}catch (Exception $exception) {
    $container['log']->error($exception->getMessage());
}


