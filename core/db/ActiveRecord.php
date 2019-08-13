<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */
namespace mini\core\db;

use mini\core\interfaces\crudInterface;
use mini\core\Model;
use mini\core\db\ActiveQuery;
use mini\core\Sam;


/**
 * Description of ActiveRecord
 *
 * @author samuel
 */
class ActiveRecord extends Model implements crudInterface
{

    public $isNewRecord = true;
    /**
     *
     * @var \mini\core\db\DB
     */
    protected $db;
    
    public function __construct() {
        $this->init();
        parent::__construct();
        
    }
    
    /**
     * make sure to call parent::init() first if you override this method
     */
    public function init()
    {
        if (!$this->hasProperty($this->primaryKey())) {
            $this->setProperties();
        }
    }

    public function create() 
    {
        $this->db->params = [];
        $op = '';
        $col = '';
        $c = 0;
        $cols = $this->getDb()->getTableColumns($this->tableName());
        foreach ($cols as $value) {
           if ($this->primaryKey() != $value){
               $op .= ($c == 0) ? '?' : ', ?';
               $col .= ($c == 0) ? $value : ', '.$value;
               array_push($this->db->params, $this->$value);
               ++$c;
           }
        }
        $this->db->sql = 'INSERT INTO '.$this->tableName().
           ' ('.$col.') VALUES ('.$op.')';
        return $this->db->insert();
    }
    
    public function save()
    {
        if ($this->isNewRecord) {
            $id = $this->create();
            if ($id) {
                $this->isNewRecord = 0;
                $this->id = $id;
                return true;
            }
        } else {
            if ($this->update()) {
                return true;
            }
        }
        return false;
    }

    public function update() 
    {
        $this->db->params = [];
        $op = '';
        $c = 0;
        $cols = $this->getDb()->getTableColumns($this->tableName());
        foreach ($cols as $value) {
           if ($this->primaryKey() != $value){
               $op .= ($c == 0) ? "$value = ?" : ", $value = ?";
               array_push($this->db->params, $this->$value);
               ++$c;
           } else {
               $key_value = $this->$value;
           }
        }
        $this->db->sql = 'UPDATE '.$this->tableName().
           ' SET '.$op.' WHERE '.$this->primaryKey().' = '.$key_value;
         $this->db->insert();
         return $this->db->preparedStatement->rowCount();
    }
    
    public function delete() 
    {
        $key = $this->primaryKey();
        $this->db->params = [$this->$key];
        $this->db->sql = 'DELETE FROM '.$this->tableName().
           ' WHERE '.$key.' = ?';
         $this->db->insert();
         return $this->db->preparedStatement->rowCount();
    }
    
    public static function tableName()
    {
        $class = new \ReflectionClass(get_called_class());       
        return strtolower(trim($class->name, $class->getNamespaceName()));
    }
     
    public static function primaryKey()
    {
        return 'id';
    }
    
    public static function getName()
    {
        $class = new \ReflectionClass(get_called_class());
        return trim($class->name, $class->getNamespaceName());
    }
    
    public static function getClass()
    {
        $class = new \ReflectionClass(get_called_class());
        return $class->name;
    }

    private function setDb()
    {
        $this->db = Sam::$ony->getDb();
    }

    /**
     * 
     * @return \mini\core\db\DB
     */
    public function getDb()
    {
        if (!$this->db) {
            $this->setDb();
        }
        return $this->db;
    }
    
    /**
     * 
     * @return ActiveQuery
     */
    public static function find()
    {
        return (new ActiveQuery(get_called_class()));
    }
    
    public function findOne($condition = [])
    {
        $st = ' WHERE ';
        $c = 0;
        $input_parameters = [];
        foreach ($condition as $key => $value) {
            if (!$this->hasProperty($key)) {
                 throw new \Exception('trying to get property: '.$key.' that does not exist');
            }
            $st .= ($c > 0) ? " AND $key = ?" : " $key = ?";
            $c += 1;
            array_push($input_parameters, $value);
        }
        $this->db->sql = 'SELECT * FROM '. $this->tableName().$st;
        $this->db->prepare();
        return (!$this->db->execute($input_parameters)) ? null :
            $this->db->preparedStatement->fetch(\PDO::FETCH_OBJ);
    }
    
    public function findAll($condition)
    {
        $st = ' WHERE ';
        $c = 0;
        $input_parameters = [];
        $class = new \ReflectionClass(get_called_class());
        foreach ($condition as $key => $value) {
            if (!$class->hasProperty($key)) {
                 throw new \Exception('trying to get property: '.$key.' that does not exist');
            }
            $st .= ($c > 0) ? " AND $key = ?" : " $key = ?";
            $c += 1;
            array_push($input_parameters, $value);
        }
        $this->db->sql = 'SELECT * FROM '. $this->tableName().$st;
       $this->db->prepare();
       return (!$this->db->execute($input_parameters)) ? null :
            $this->db->preparedStatement->fetch(\PDO::FETCH_CLASS, $class->name);
    }

    /**
     * 
     * @param string $name
     * @return string|null
      * @throws Exception
     */
    protected function getAttribute($name)
    {
        return $this->$name;
    }

        /**
     * 
     * @return array
     */
    protected function getAttributes()
    {
        $arr = [];
        $class = new \ReflectionClass(get_called_class());
        if (!$this->hasProperty($class->getMethod('primaryKey')->invoke(NULL))) {
            $arr1 = $this->setProperties();
        }
        
        foreach ($class->getProperties() as $property) {
            array_push($arr, $property->name);
        }
        return array_merge($arr, $arr1);
    }
    /**
     * do not call this method directly
     */
    public function setProperties()
    {
        $db = $this->getDb();
        $array = $db->getTableColumns($this->tableName());
        foreach ($array as $property) {
            $this->{$property} = '';
        }
        $db->preparedStatement = '';
        $db->sql = '';
        return $array;
    }
}
