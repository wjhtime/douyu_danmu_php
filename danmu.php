<?php

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ );
}

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/src/init.php';

$application = new \Symfony\Component\Console\Application();
$application->add(new \App\Command\DouyuCommand());
$input = new \Symfony\Component\Console\Input\ArrayInput([
    'command' => 'danmu',
]);
$application->run($input, $output);