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
    
    public $paths = [];
    
    public $query;

    public function __construct(array $config) 
    {
        $this->setRoutes($config);
        $this->setMethod();
    }

    public function __destruct() {
        $this->vars = [];
        $this->variable_name = '';
        $this->isPost = false;
        $this->isGet = false;
        $this->paths = [];
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->$name;
    }
    
    private function setUri($config)
    {
        $array = str_replace(HOMEURL, '', parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI')));
        $this->paths = explode('/', trim($array['path'], '/')); 
        $this->query = (array_key_exists('query', $array)) ? $array['query'] : '';       
        if ($config["url"]["pretty"] == false) {
            $parts = explode('&', $this->query);
            $this->paths[0] = $parts['r'];
            $this->setController($config);
            $this->paths[1] = ($parts['a'] && !$this->paths[1]) ? : $this->getController()->defaultAction;
            $this->query  = implode('&', array_slice($parts, 2));                        
        } else {
            if (count($this->paths) == 1) {
                if ($this->paths[0] == '') { //no controller specified
                    $this->paths = explode('/', $config['defaultRoute']);
                    $this->setController($config);
                } else { //only controller was provided
                    $this->setController($config);
                    $this->paths[1] = (array_key_exists(1, $this->paths))? $this->paths[1] : $this->getController()->defaultAction;
                }       
            } else {//both controller and action were supplied
                $this->setController($config);
            }
        }
        return $this;
    }

    private function setRoutes($config)
    {
        $this->setUri($config);
        $this->setAction($config);
        if ($this->query) {
            if (strpos($this->query, '&') !== FALSE) {
                $params = explode('&', $this->query);
            } else {
                $params = [$this->query];
            }
            foreach ($params as $value) {
                $param = explode('=', $value);
               (array_key_exists(0, $param)) ? $this->params[$param[0]] = $param[1] : "";
            }           
        }
    }
    
    private function setController($config)
    {
       $name =  $this->paths[0];
       $controller = 'mini\controllers\\'.ucfirst($name).'Controller';
       if (!class_exists($controller)){
           $this->paths = explode('/', $config['error']);
           $controller = 'mini\controllers\\'.ucfirst($this->paths[0]).'Controller';
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
        $this->action =  $this->paths[1];
        $class = new \ReflectionClass($this->controller);
        $this->controller = $class->newInstanceWithoutConstructor();
        if (!$this->controller->hasMethod('action'.ucwords($this->action))) {
            $this->paths = explode('/', $config['error']);
            $this->setController($config);
        }
    }

    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    public function getQuery()
    {
        return $this->query;
    }

    
    private function setMethod()
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
