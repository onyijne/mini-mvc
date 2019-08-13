<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\db;

use mini\core\interfaces\pdoInterface;
use PDO;
/**
 * Description of DB
 *
 * @author samuel
 */
abstract class DB implements pdoInterface
{
    /**
     *
     * @var string The database name
     */
    public $name;
    /**
     *
     * @var array PDO options
     */
    public $options = [];
    
    /**
     *
     * @var array the parameters for execute method, when inserting multiple records at a time
     * this should be an array of arrays.
     */
    public $params;

    /**
     *
     * @var \PDOStatement PDO preparedStatement object
     */
    public $preparedStatement;
    
    /**
     *
     * @var mixed string|array|object gotten from the executed query
     */
    public $results;

    /**
     *
     * @var string the SQL to execute
     */
    public $sql;
      /**
     *
     * @var string 
     */
      public $dsn;
    
    /**
     *
     * @var string
     */
    public $username;
    
    /**
     *
     * @var string
     */
    public $password;
    
    /**
     *
     * @var string
     */
    public $charset = 'utf8';
    
    /**
     * @var PDO
     */
    public $pdo;
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
    
    public function __get($name) {
        return $this->$name;
    }
    
    protected function connect()
    {
        if ($this->pdo == null) {
            try {
               $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
            } catch (\PDOException $exc) {
               echo $exc->getMessage();
               die();
            }           
        }
        return $this->pdo;
    }
    
    protected function close()
    {
        $this->pdo = null;
    }
    

    public function getDbName(){}
    
    /**
     * 
     * 
     * @return \PDOStatement
     */
    public function prepare() {
        $this->preparedStatement = $this->pdo->prepare($this->sql);
        return $this->preparedStatement;
    }
    
    public function execute(array $input_parameters = null) {
        return $this->preparedStatement->execute($input_parameters);
    }
    
    
    /**
     * 
     * @return \PDOStatement
     */
    public function query() {
        return $this->preparedStatement = $this->pdo->query($this->sql);
    }
    
    public function fetchAll($fetch_style, $class_name = null)
    {
        return $this->preparedStatement->fetchAll($fetch_style, $class_name);
    }
    
    public function fetchOne($fetch_style)
    {
        return $this->preparedStatement->fetch($fetch_style);
    }
    
    /**
     * performs the actual updating of data into the database.
     * this method should be overridden by a driver class to perform the expected task
     */
    public function update(){}
    
    /**
     * performs the actual updating of data into the database.
     * this method should be overridden by a driver class to perform the expected task.
     * this method should be used for multiple update so as to use the same prepared statement
     */
    public function updateMultiple(){}
    
    /**
     * performs the actual insertion of data into the database.
     * this method should be overridden by a driver class to perform the expected task
     */
    public function insert(){}
    
    /**
     * performs the actual insertion of data into the database.
     * this method should be overridden by a driver class to perform the expected task.
     * this method should be used for multiple insertion so as to use the same prepared statement
     */
    public function insertMultiple(){}
}
