<?php

use App\Lib\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

ini_set('date.timezone', 'PRC');

$app = require APP_ROOT. '/config/app.php';
$config = Config::instance($app);

$log = new Logger('douyu');
$log->pushHandler(new StreamHandler($config['log_file']));
