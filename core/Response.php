<?php

/*
 * Check LICENSE file for license terms
 * (c) 2019. Samuel Onyijne, <samuel@sajflow.com>  * 
 */

namespace mini\core;

/**
 * Description of Response
 *
 * @author samuel
 */
class Response 
{
    const FORMAT_HTML = "text/html";
    
    const FORMAT_JSON = "application/json";

    public $format = 'text/html';
    
    protected $data;
    
    public function __construct() {
        
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

        public function send()
    {
        $this->setHeader("Content-Type", $this->format);
        if ($this->format == 'text/html') {
            return $this->data;
        }
        
        if ($this->format == 'application/json') {
            echo json_encode($this->data);
        }
    }
    
    private function setHeader($name = "Content-Type", $value = "text/html")
    {
        header($name.': '.$value);
    }
}
