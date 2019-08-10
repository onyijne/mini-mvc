<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\controllers;

use mini\core\Sam;
use mini\core\Response;
use mini\models\User;

/**
 * Description of UserController
 *
 * @author samuel
 */
class UserController extends \mini\core\Controller
{
    
    public function __construct() {
        Sam::$ony->getResponse()->format = Response::FORMAT_JSON;
        parent::__construct();
    }

    public function actionIndex()
    {
        return (new User())->_data;
    }

    public function actionView($id)
    {
        return (new User())->demo($id);
    }
    
    public function actionCreate()
    {
        if (!Sam::$ony->getRequest()->isPost) {
            throw new Exception('This method only accept post requests', 400);
        }
        return User::addNew();
    }
    
    public function actionDelete($id)
    {
        if (!Sam::$ony->getRequest()->isPost) {
            throw new Exception('This method only accept post requests', 400);
        }
        $model = (new User())->demo($id);
        if (!$model) {
            return [
                'status' => 'error',
                'message' => $id. ' not on file'
            ];
        }
        if (!$model->delete()) {
            return [
                'status' => 'error',
                'message' => $id. ' could not be deleted.'
            ];
        }
        return [
            'status' => 'success',
            'message' => $id.' was deleted successfully'
        ];
    }
}
