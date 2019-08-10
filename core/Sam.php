<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */
namespace mini\core;

use mini\core\Request;
use mini\core\Response;

defined('BASE_PATH') or define('BASE_PATH', dirname(__DIR__));
defined('CONTROLLERS_PATH') or define('CONTROLLERS_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'controllers');
defined('MODELS_PATH') or define('MODELS_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'models');
defined('VIEWS_PATH') or define('VIEWS_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR.'views');


/**
 * Description of Sam
 *
 * @author samuel
 */
class Sam 
{
    /**
     *
     * @var Sam
     */
    public static $ony;
    
    private static $db;
    
    /**
     *
     * @var Request
     */
    private $request;
    /**
     *
     * @var Response
     */
    private $response;
    
    private $configs = [];

    public function __construct($config) 
    {
        $this->configs = array_merge($this->defaults(), $config);
        $this->setRequest();
        $this->setDb();
        static::$ony = $this;
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function run()
    {
        return $this->handleRequest();
    }
    
    private function defaults()
    {
        return [
            "url" => [
                "pretty" => true //for simplicity sake, this only implies to controller (r) and action (a)
            ],
            'defaultRoute' => 'app/index',
            'error' => 'app/error'
        ];
    }

        private function setDb()
    {
        
    }

        /**
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    private function setRequest()
    {       
        $this->request = new Request(($this->configs));
        return $this->request;
    }
    
    private function handleRequest()
    {
        $controller = $this->request->controller;
        $class = new \ReflectionClass($controller);
        $name = array_slice(explode('\\', $class->getName()), 2);
        if (!file_exists(CONTROLLERS_PATH.DIRECTORY_SEPARATOR.implode('', $name).'.php')) {
            $class = new \ReflectionClass('mini\controllers\AppController');
            $action = 'actionError';           
            $c = $class->newInstance([]);
            return $c->$action();
        } else {
            $action = 'action'.ucfirst($this->request->action);
          //  $c = $class->newInstance([]);
            $refP = new \ReflectionMethod($class->getName(), $action);
            $params = [];
            foreach ($refP->getParameters() as $value) {
                if (!array_key_exists($value->name, $this->request->params)) {
                    throw new \Exception('Missing required params in '.$class->getName().': '.$action);
                }
                $params[$value->name] = $this->request->params[$value->name];
            }         
            $this->setResponse()->setData($refP->invokeArgs($class->newInstance([]), $params));
            return $this->response->send();
        }
        
    }
    
    /**
     * 
     * @return Response
     */
    public function getResponse()
    {
        return $this->response; 
    }
    /**
     * 
     * @return Response
     */
    private function setResponse()
    {
        $this->response = new Response();
        return $this->response;
    }
    
    public function assignValues($object, array $properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($object, $key)) {
                ($value != null) ? $object->$key = $value  : "";
            }
        }
        return $object;
    }
}
