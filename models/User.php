<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\models;

use mini\core\Sam;

/**
 * Description of User
 *
 * @author samuel
 */
class User 
{
    public $id;
    public $name;
    public $email;
    /**
     *
     * @var array used to mock a database
     */
    public $_data = [];


    public function __construct() {
        $this->_data = self::data();
    }

    public function demo($id)
    {
        return (array_key_exists($id, $this->_data)) ? $this->_data[$id] : [];
    }
    
    private static function data()
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Arien',
                'email' => 'arien@sajflow.com'
            ],
            2 => [
                'id' => 2,
                'name' => 'Samuel',
                'email' => 'samuel@sajflow.com'
            ],
            3 => [
                'id' => 3,
                'name' => 'Arien Samuel',
                'email' => 'arien.samuel@sajflow.com'
            ],
        ];
    }
    
    public static function addNew()
    {
        $post = Sam::$ony->getRequest()->post();
        $model = Sam::$ony->assignValues(new User, $post);
        if (!$model->save()) {
            return [
                'status' => 'error',
                'message' => ' new record could not be added.'
            ];
        }
        return [
            'status' => 'success',
            'message' => 'new record was added',
            'current_data' => $model->_data
        ];
    }
    
    public function save()
    {
        $this->id = count($this->_data) + 1;
        array_push($this->_data, [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ]);
        return true;
    }
    
    public function delete()
    {
        unset($this->_data[$this->id]);
        return true;
    }
    
    
}
