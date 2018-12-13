<?php

ini_set('date.timezone', 'PRC');

$app = require APP_ROOT. '/config/app.php';
$config = \App\Lib\Config::instance($app);

define('DEBUG', $config['debug']);
define('SHOW_TIME', $config['show_time']);
define('SHOW_COLOR', $config['show_color']);
