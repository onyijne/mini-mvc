<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\interfaces;

/**
 *
 * @author samuel
 */
interface crudInterface {
    
    public function create();
    public function delete();
    public function update();
    public static function tableName();
}
