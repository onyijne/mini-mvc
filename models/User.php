<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\models;

use mini\core\Sam;
use mini\core\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * 
 * Description of User
 *
 * @author samuel
 */
class User  extends ActiveRecord
{
    
    public static function tableName() {
        return 'user';
    }
    
}
