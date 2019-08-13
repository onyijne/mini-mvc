<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core;

/**
 * Description of Model
 *
 * @author samuel
 */
class Model implements \Iterator
{    
    private $position = 0;
    private $array = [];  

    /**
     * this method should be called first when overridden by a child class
     * like parent::__construct()
     */
    public function __construct() {
        $this->position = 0;
        $array = [];
        $class = new \ReflectionClass(get_called_class());
         if ($class->hasMethod('tableName')) {
            $mode = $class->newInstanceWithoutConstructor();
            $array = $mode->setProperties();
        }
        foreach ($class->getProperties() as $property):
            array_push($this->array, $property->name);
        endforeach;
        $this->array = array_merge($this->array, $array);
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->array[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->array[$this->position]);
    }
    
    protected function setAttribute($name, $value)
    {
        $this->$name = $value;
    }
    
    protected function setAttributes(array $values)
    {
        $model = (new \ReflectionClass(get_called_class()))->newInstanceArgs();
       
        foreach ($values as $key => $val):
         if (!$model->hasProperty($key)) {
             throw new \Exception($key. ' is not a property of '.$model->getClass());
         }
        endforeach;
        
        foreach ($model as $key => $property):
        (array_key_exists($property, $values)) ?
          $this->setAttribute($property, $values[$property]) : '';
        endforeach;
        return true;
    }
    
    public function load($post)
    {
        if(!is_array($post)) {
            return false;
        }
        $this->setAttributes($post);
        return true;
    }
    
    public function hasProperty($name)
    {
        $re = false;
        foreach ($this as $property):
            ($name == $property) ? $re = true : '';
        endforeach;
        return $re;
    }
   
}
