<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\db;

use mini\core\db\DB;
use PDO;
/**
 * Description of Sqlite
 *
 * @author samuel
 */
class Sqlite extends DB
{
    use \mini\core\traits\DbTrait;
    
    public function __construct($db) {
       $this->dsn = $db['dsn'];
       $this->options = [
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        //   PDO::ATTR_EMULATE_PREPARES => false
       ];
       $this->connect();
    }
    
    public function __sleep() {
        $this->close();
    }
    
    public function __wakeup() {
        $this->connect();
    }
    
    protected function setName()
    {
        if ($this->dsn == ":memory") {
            return $this->name = ':memory';
        }
        $array = explode(DIRECTORY_SEPARATOR, $this->dsn);
        foreach ($array as $value) {
            (!next($array)) ? $this->name = $value : '';
        }
        if (is_int(strpos($this->name, ';'))) {
            $this->name = explode(';', $this->name)[0];
        } 
    }
    
    public function insert()
    {
        try {
            $this->pdo->beginTransaction();
            $this->prepare();
            $this->execute($this->params);
            $id = $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $id;
        } catch (Exception $exc) {
            $this->pdo->rollBack();
            throw $exc;
        }
    }
    
    public function insertMultiple()
    {
        try {
            $this->pdo->beginTransaction();
            $this->prepare();
            foreach ($this->params as $array) {
                $this->execute($array);
            }
            $id = $this->pdo->lastInsertId();
            $this->pdo->commit();
            return $id;
        } catch (Exception $exc) {
            $this->pdo->rollBack();
            throw $exc;
        }
    }
    
    /**
     * 
     * @param string $table_name The table name
     * @return array an array of arrays of the columns properties or an empty array
     */
    public function getColumnsSchema($table_name)
    {
        $this->sql = "PRAGMA table_info($table_name)";
        $this->prepare();
        if ($this->execute()) {
            $arr = $this->preparedStatement->fetchAll();
            $this->preparedStatement = '';
            return $arr;
        } else {
            return null;
        }
    }
    
    /**
     * 
     * @param string $table_name The table name
     * @return array an array of the column names or an empty array
     */
    public function getTableColumns($table_name)
    {
        $this->sql = "PRAGMA table_info($table_name)";
        $this->prepare();
        if ($this->execute()) {
            $arr = $this->preparedStatement->fetchAll();
            $this->preparedStatement = '';
            return array_column($arr, 'name');
        } else {
            return [];
        }
    }
    
    public function demoTableCode()
    {
        return "CREATE TABLE IF NOT EXISTS task (
 id INTEGER PRIMARY KEY,
 user_id INTEGER NOT NULL,
 title TEXT NOT NULL,
 description TEXT NOT NULL,
 creation_date TEXT NOT NULL,
 status INTEGER NOT NULL
); 
CREATE TABLE IF NOT EXISTS user (
 id INTEGER PRIMARY KEY,
 name TEXT NOT NULL,
 email TEXT NOT NULL UNIQUE
) ";
    }

}
