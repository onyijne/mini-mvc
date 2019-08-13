<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */
namespace mini\core\traits;
use mini\core\Sam;
use mini\core\db\DB;
/**
 *
 * @author samuel
 */
trait DbTrait {
    
    
    
    public function createTable($sql)
    {
        $this->params = [];
        $this->sql = $sql;
        $this->pdo->exec($this->sql);
    }
    
    
}
