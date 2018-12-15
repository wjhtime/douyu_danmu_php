<?php

use App\Lib\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Pimple\Container;

ini_set('date.timezone', 'PRC');

$app = require APP_ROOT. '/config/app.php';

$container = new Container();
$container['config'] = function (Container $container) use ($app) {
    Config::instance($app);
    return $app;
};

$container['log'] = function (Container $c) {
    $log = new Logger('douyu');
    $log->pushHandler(new StreamHandler($c['config']['log_file']));
};



