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
interface pdoInterface {
    public function prepare();
    public function execute(array $input_parameters = null);
    public function query();
}
