<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of request
 *
 * @author adrian
 */
class request {
    
    protected $GET = array();
    
    protected $POST = array();
    public function __get($index) {
        if (isset($this->GET[$index]))
        return $this->GET[$index];
        if (isset($this->POST[$index]))
        return $this->POST[$index];
        return null;
    }

    public function setPOST($index, $value) {
        $this->POST[$index] = $value;
    }
    public function setGET($index, $value) {
        $this->GET[$index] = $value;
    }
    function addPOST($post)
    {
       $this->POST = $post; 
    }
        
        
    
}


