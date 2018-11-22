<?php

use App\Pcntl;


define('DEBUG', true);

if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ );
}

require APP_ROOT . '/vendor/autoload.php';


require APP_ROOT . '/src/init.php';

Pcntl::handle();
