<?php
$base = dirname(__DIR__);
defined('SAM_DEV') or define('SAM_DEV', 'dev');//you can comment out in production
/* 
* /sub/folder (without an ending forward slash) when not in root. 
* in production it is recommended you put other file under your web-server un-accessible directory.
*/
defined('HOMEURL') or define('HOMEURL', '/public');
include $base.'/Autoload.php';
include $base.'/config/main.php';
$app = new \mini\core\Sam($config);
$app->run();
