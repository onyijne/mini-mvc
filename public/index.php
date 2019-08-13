<?php
$base = dirname(__DIR__);
defined('SAM_DEV') or define('SAM_DEV', 'dev');//you can comment out in production
defined('HOMEURL') or define('HOMEURL', '');/* /sub/folder (without an ending forward slash) when not in root*/
include $base.'/Autoload.php';
include $base.'/config/main.php';
$app = new \mini\core\Sam($config);
$app->run();
