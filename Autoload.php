<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com> 
 */


/**
 * Description of Autoload
 *
 * @author samuel
 */
class Autoload 
{
    public static function register()
    {
        spl_autoload_register(function($class) {
            $file = dirname(__DIR__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists($file)) {
                require $file;
                return true;
            }
            return false;
        });
    }
}
Autoload::register();
