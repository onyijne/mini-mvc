<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\models;

use mini\core\Sam;

/**
 * Description of Task
 *
 * @author samuel
 */
class Task 
{
    public $id;
    public $user_id;
    public $title;
    public $description;
    public $creation_date;
    public $status;

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
        return (array_key_exists($id, $this->_data)) ? $this->_data[$id] : null;
    }
    
    private static function data()
    {
        return [
            1 => [
                'id' => 1,
                'user_id' => 2,
                'title' => 'mock data',
                'description' => 'not from db',
                'creation_date' => date('m-d-Y'),
                'status' => 1
            ],
            2 => [
                'id' => 2,
                'user_id' => 3,
                'title' => 'second mock data',
                'description' => 'taken from array',
                'creation_date' => date('m-d-Y', strtotime('yesterday')),
                'status' => 1
            ],
            3 => [
                'id' => 3,
                'user_id' => 1,
                'title' => 'another mock data',
                'description' => 'not persistant yet',
                'creation_date' => date('m-d-Y', strtotime('last week')),
                'status' => 1
            ],
        ];
    }
    
    public static function addNew()
    {
        $post = Sam::$ony->getRequest()->post();
        $model = Sam::$ony->assignValues(new Task, $post);
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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'creation_date' => $this->creation_date,
            'status' => $this->status
        ]);
        return true;
    }
    
    public function delete()
    {
        unset($this->_data[$this->id]);
        return true;
    }

    public function update()
    {
        $post = Sam::$ony->getRequest()->post();
        $model = Sam::$ony->assignValues($this, $post);
        $this->_data[$this->id] =[
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'creation_date' => $this->creation_date,
            'status' => $this->status
        ]; //just for simplicity sake since mocking db
        return [
            'status' => 'success',
            'message' => 'new record was added',
            'current_data' => $model->_data
        ];
    }

    public function getModel($id)
    {
        $c = new \ReflectionClass($this);
        $model = Sam::$ony->assignValues($this, $this->_data[$id]);
        foreach ($c->getProperties() as $key => $value):
        $field = $this->_data[$id];
        $model->$value->name = $field[$value->name];
        endforeach;
        return $model;
    }
    
    
}
