<?php
$base = dirname(__DIR__);
defined('HOMEURL') or define('HOMEURL', '');/* /sub/folder (without an ending forward slash)*/
include $base.'/Autoload.php';
include $base.'/config/main.php';
$app = new \mini\core\Sam($config);
$app->run();