<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */
namespace mini\core;

use mini\core\Request;
use mini\core\Response;
use mini\core\db\Sqlite;
use mini\core\db\Mysql;

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
    
    /**
     *
     * @var \mini\core\db\DB
     */
    public $db;
    
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
    
    public $configs = [];

    public function __construct($config) 
    {
        if (SAM_DEV) {
            ini_set('display_errors', 1);
        }
        $this->configs = array_merge($this->defaults(), $config);
        $this->setRequest();
        self::$ony = $this;
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
            'defaultRoute' => 'app/home',
            'error' => 'app/error',
            'defaultConroller' => 'mini\controllers\AppController'
        ];
    }

    public function setDb($db)
    {
        if (empty($db)) {
            throw new \Exception('No db is present in the config');
        }
        $driver = strstr($db['dsn'], ':', true);
        switch ($driver) {
            case 'sqlite':
                $this->db = new Sqlite($db); 
                $this->db->getDbName();
                $this->db->createTable($this->db->demoTableCode());
                break;
            case 'mysql':
                $this->db = new Mysql($db);
                $this->db->getDbName();
                break;
            default:
                throw new \Exception('database driver not supported');
        }
        
    }
    /**
     * 
     * @return \mini\core\db\DB
     */
    public function getDb()
    {
        if ($this->db == null) {
            $this->setDb($this->configs['db']);
        }
        return $this->db;
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
        $action = 'action'.ucfirst($this->request->action);
        $refMethod = new \ReflectionMethod($class->getName(), $action);
        $params = [];
        foreach ($refMethod->getParameters() as $value) {
            if (!array_key_exists($value->name, $this->request->params)) {
                $paths = explode('/', $this->configs['error']);
                $controller = 'mini\controllers\\'.ucfirst($paths[0]).'Controller';
                $action = 'action'.ucfirst($paths[1]);
                $class = new \ReflectionClass($controller);
                $refMethod = new \ReflectionMethod($class->getName(), $action);
               $message = 'Missing required parameter';
               $this->setResponse()->setData($refMethod->invoke($class->newInstance([])));
               $params = ['message'=>$message];
               break;
            } else {
                $params[$value->name] = $this->request->params[$value->name];
            }
        }         
       $this->setResponse()->setData($refMethod->invokeArgs($class->newInstance([]), $params));
        return $this->response->send();     
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
    
    public static function assignValues($object, array $properties)
    {
        foreach ($properties as $key => $value) {
            if (property_exists($object, $key)) {
                ($value != null) ? $object->$key = $value  : "";
            }
        }
        return $object;
    }
    
}
