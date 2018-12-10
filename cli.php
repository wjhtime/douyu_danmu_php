<?php

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ );
}

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/src/init.php';

$application = new \Symfony\Component\Console\Application();

array_map(function ($cmd) use ($application) {
    $application->add(new $cmd);
}, $config['commands']);

$application->run();

