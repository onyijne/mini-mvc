<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core\rest;

use mini\core\Sam;
use mini\core\Response;

/**
 * The base Controller
 *
 * @author samuel
 */
abstract class Controller 
{
    /**
     *
     * @var \mini\core\db\ActiveRecord
     */
    private $model;
    protected $layout = '';
    
    public $modelClass;

    public $defaultAction = 'index';

    public $data = [];
    
    public function __construct() {
        if (!$this->modelClass) {
            throw new \Exception('modelClass is required for Restful controllers');
        }
        Sam::$ony->getResponse()->format = Response::FORMAT_JSON;
    }
    
    public function actionIndex()
    {
        $model = $this->getModel();
        return $model->find()->all();
    }
    
     public function actionView($id)
    {
        $model = $this->getModel();
        $re = $model->findOne(['id' => $id]);
        return ($re) ? : [
            'status' => 'error',
            'message' => 'no record found'
        ];
    }
    
    public function actionCreate()
    {
        $request = Sam::$ony->getRequest();
        if (!$request->isPost) {
            return [
                'status' => 'error',
                'message' => 'method not supported'
            ];
        }
        $model = $this->getModel();
        if ($model->load($request->post())) {
            return ($model->save()) ?
             [
            'status' => 'success',
            'message' => ' data insert was successfully',
            'id' => $model->id
        ]:[
                'status' => 'error',
                'message' => 'data insertion failed?'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'empty post request'
            ];
        }
    }
    
    public function actionUpdate()
    {
        $request = Sam::$ony->getRequest();
        if (!$request->isPost) {
            return [
                'status' => 'error',
                'message' => 'method not supported'
            ];
        }
        $model = $this->getModel();
        $model->isNewRecord = 0;
        if ($model->load($request->post())) {
            return ($model->update()) ?
             [
            'status' => 'success',
            'message' => ' data update was successfully',
            'id' => $model->id
        ]:[
                'status' => 'error',
                'message' => 'data insertion failed?'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'empty post request'
            ];
        }
    }
    
    public function actionDelete()
    {
        $request = Sam::$ony->getRequest();
        if (!$request->isPost) {
            return [
                'status' => 'error',
                'message' => 'method not supported'
            ];
        }
        $model = $this->getModel();
        $model->isNewRecord = 0;
        if ($model->load($request->post())) {
            return ($model->delete()) ?
             [
            'status' => 'success',
            'message' => ' data was deleted successfully',
        ]:[
                'status' => 'error',
                'message' => 'data deleting failed?'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'empty post request'
            ];
        }
    }

    public function set($data)
    {
        $this->data = array_merge($data, $this->data);
    }
    
    public function hasMethod($name)
    {
        if (!method_exists($this, $name)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    private function setModel()
    {
        $this->model = (new \ReflectionClass($this->modelClass))->newInstance([]);
    }
    
    protected function getModel()
    {
        if (!$this->model) {
            $this->setModel();
        }
        return $this->model;
    }
}
