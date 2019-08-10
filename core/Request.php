<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core;

/**
 * Description of Request
 *
 * @author samuel
 */
class Request 
{
    public $isGet;
    
    public $isPost;
    
    public $isPut;
    
    public $isDelete;
    /**
     *
     * @var Controller 
     */
    public $controller;
    
    public $action;
    
    public $params;
    
    private $vars = [];
    
    private $variable_name;
    
    private $uri;
    
    private $paths;

    public function __construct(array $config) 
    {
        $this->setRoutes($config);
        $this->setsMethod();
    }

    public function __destruct() {
        $this->vars = [];
        $this->variable_name = '';
        $this->isPost = false;
        $this->isGet = false;
        $this->uri = [];
    }
    
    private function setUri($config)
    {
        $array = str_replace(HOMEURL, '', parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI')));
        $this->paths = explode('/', trim($array['path'], '/'));              
        if (count($this->paths) == 1) {
            if ($this->paths[0] == '') { //no controller specified
                $this->uri = explode('/', $config['defaultRoute']);
                $this->setController($config);
            } else { 
                $this->uri[0] = $this->paths[0];
                $this->setController($config);
                $this->uri[1] = $this->getController()->defaultAction;
            }       
        } else {
            $this->uri[0] = $this->paths[0];
            $this->uri[1] = $this->paths[1];
            $this->setController($config);
        }
        
        if ($config["url"]["pretty"] == false) {
            $part = explode('&', $array['query']);
            $this->uri[0] = $this->paths[0] = $part['r'];
            $this->uri[1] = $this->paths[1] = $part['a'];
            $this->uri[2] = array_slice($part, 2);                        
        } else {
            $this->uri[2] = (array_key_exists('query', $array)) ? $array['query'] : [];
        }
    }

    private function setRoutes($config)
    {
        $this->setUri($config);
        $this->setAction($config);
        if (array_key_exists(2, $this->uri)) {
            $params = $this->uri[2];
            if (strpos($params, '&') !== FALSE) {
                $params = explode('&', $params);
            } else {
                $params = [$params];
            }
            foreach ($params as $value) {
                $param = explode('=', $value);
                $this->params[$param[0]] = $param[1];
            }
            
        }
    }
    
    private function setController($config)
    {
       $name =  $this->uri[0];
       $controller = 'mini\controllers\\'.ucfirst($name).'Controller';
       if (!class_exists($controller)){
           $this->uri = explode('/', $config['error']);
           $controller = 'mini\controllers\\'.ucfirst($this->uri[0]).'Controller';
       } 
       $class = new \ReflectionClass($controller);
       $this->controller = $class->newInstanceWithoutConstructor();
    }

    public function getController()
    {
        return $this->controller;
    }
    
    private function setAction($config)
    {
        $this->action =  $this->uri[1];
        $class = new \ReflectionClass($this->controller);
        $this->controller = $class->newInstanceWithoutConstructor();
        if (!$this->controller->hasMethod('action'.ucwords($this->action))) {
            $this->uri = explode('/', $config['error']);
            $this->setController($config);
            $this->action = 'error';
        }
    }

    public function getAction()
    {
        return $this->action;
    }
    
    public function getParams()
    {
        return $this->params;
    }

    
    private function setsMethod()
    {
        switch (filter_input(INPUT_SERVER, 'REQUEST_METHOD')) {
            case 'GET':
                $this->isGet = true;
                break;
            case 'POST':
                $this->isPost = true;

            default:
                break;
        }
    }

    public function get($name = '')
    {
        if ($name) {
            return $this->variable_name = filter_input(INPUT_GET, $name);
        }
        return $this->vars = filter_input_array(INPUT_GET);
    }
    
    public function post($name = '')
    {
        if ($name) {
            return $this->variable_name = filter_input(INPUT_POST, $name);
        }
        return $this->vars = filter_input_array(INPUT_POST);
    }
    
}
