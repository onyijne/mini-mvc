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
 * @property int $$user_id
 * @property string $title
 * @property string $description
 * @property strinf $creation_date
 * @property int $status
 * 
 * Description of Task
 *
 * @author samuel
 */
class Task extends ActiveRecord
{

    public static function tableName() {
        return 'task';
    }
    
}
