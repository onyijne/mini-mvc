<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\db;

use mini\core\db\ActiveRecord;
use mini\core\Sam;
/**
 * Description of ActiveQuery
 *
 * @author samuel
 */
class ActiveQuery 
{    
    /**
     *
     * @var DB 
     */
    private $db;
    /**
     *
     * @var ActiveRecord
     */
    protected $model;
    
    protected $query;



    /**
     * 
     * @param ActiveRecord $class
     */
    public function __construct($class) {
         $this->model = (new \ReflectionClass($class))->newInstanceArgs();
         $this->db = Sam::$ony->getDb();
    }
    
    public function one()
    {
        
    }
    
    public function all()
    {
        if ($this->db->preparedStatement == null) {
            $this->db->sql = 'SELECT * FROM '.$this->model->tableName();
            $st = $this->db->query();
            return $st->fetchAll();
        } else {
             $this->db->execute($this->db->params);
             return $this->db->fetchAll(\PDO::FETCH_CLASS, $this->model->getClass());
        }
    }
    
    public function first()
    {
        
    }
    
    public function last()
    {
        
    }
    
    public function unique()
    {
        
    }
    
    public function groupBy($column)
    {
        
    }
}
