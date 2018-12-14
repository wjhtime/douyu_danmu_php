<?php

use App\Lib\Config;

ini_set('date.timezone', 'PRC');

$app = require APP_ROOT. '/config/app.php';
$config = Config::instance($app);
