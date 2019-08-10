<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\controllers;

use mini\core\Sam;
use mini\core\Response;

/**
 * Description of AppController
 *
 * @author samuel
 */
class AppController extends \mini\core\Controller
{

    public function actionHome()
    {
        return $this->render('home',[
            'task' => Sam::$ony->getRequest()->get('task'),
            'id' => Sam::$ony->getRequest()->get('id'),
           ]);
    }

    public function actionError()
    {
        return $this->render('error');
    }
}
