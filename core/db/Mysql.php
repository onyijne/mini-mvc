<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\db;

use mini\core\db\DB;
use PDO;

/**
 * Description of Mysql
 *
 * @author samuel
 */
class Mysql extends DB 
{
    use \mini\core\traits\DbTrait;

    public function __construct($db) {
       $this->username = $db['username'];
       $this->password = $db['password'];
       $this->dsn = (array_key_exists('charset', $db)) ?
               $db['dsn'].';'.$db['charset'] : $db['dsn'];
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
        $array = explode('=', $this->dsn);
        foreach ($array as $value) {
            (!next($array)) ? $this->name = $value : '';
        }
        if (is_int(strpos($this->name, ';'))) {
            $this->name = explode(';', $this->name)[0];
        }
    }
    
    public function getDbName()
    {
        (!$this->name) ? $this->setName() : '';
        return $this->name;
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
        $db = Sam::$ony->getDb();
        $db->sql = 'SHOW COLUMNS FROM '.$table_name;
        $db->prepare();
        if ($db->execute([$db->name, $table_name])) {
            $arr = $db->preparedStatement->fetchAll();
            $db->preparedStatement = '';
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
        $db = Sam::$ony->getDb();
        $db->sql = 'SELECT COLUMN_NAME as name FROM information_schema.columns WHERE table_schema= ? AND table_name = ?';
        $db->prepare();
        if ($db->execute([$db->name, $table_name])) {
            $arr = $db->preparedStatement->fetchAll();
            $db->preparedStatement = '';
            return array_column($arr, 'name');
        } else {
            return [];
        }
    }
    
    public function demoTableCode()
    {
        return "CREATE TABLE task (
 id int(11) NOT NULL AUTO_INCREMENT,
 user_id int(11) NOT NULL,
 title varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 description varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 creation_date varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 status tinyint(1) NOT NULL,
 PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; 
CREATE TABLE user (
 id int(11) NOT NULL AUTO_INCREMENT,
 name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (id),
 UNIQUE KEY email (email)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    }
}
