<?php

/* 
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */
$config = [
    "db" => require __DIR__.DIRECTORY_SEPARATOR.'_db.php',
    "url" => [
        "pretty" => true //for simplicity sake, this only implies to controller (r) and action (a)
    ],
    'defaultRoute' => 'app/home',
    'error' => 'app/error'
];

return $config;
