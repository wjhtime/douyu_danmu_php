<?php

ini_set('date.timezone', 'PRC');
define('DEBUG', false);
define('SHOW_TIME', true);

$app = require APP_ROOT. '/config/app.php';
$config = \App\Lib\Config::instance($app);


