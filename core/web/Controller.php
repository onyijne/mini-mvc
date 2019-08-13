<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\web;

/**
 * The base Controller
 *
 * @author samuel
 */
abstract class Controller 
{
    protected $layout = 'main';
    
    public $defaultAction = 'index';

    public $data = [];
    
    public function __construct() {
        
    }

        public function set($data)
    {
        $this->data = array_merge($data, $this->data);
    }

    protected function render($view, $data = [])
    {
        $c = strtolower(str_ireplace('Controller', '', get_called_class()));
        extract(array_merge($this->data, $data));
        ob_start();
        require VIEWS_PATH.DIRECTORY_SEPARATOR. trim($c, 'mini\\s\\').DIRECTORY_SEPARATOR.$view.'.php';
        $content = ob_get_clean();
        if ($this->layout) {
            require VIEWS_PATH.DIRECTORY_SEPARATOR.'layouts'.DIRECTORY_SEPARATOR.$this->layout.'.php';
        } else {
           echo $content;
        }
    }
    
    public function hasMethod($name)
    {
        if (!method_exists($this, $name)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
