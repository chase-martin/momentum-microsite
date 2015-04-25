<?php

/**
 * a class set and get session values to $_SESSION
 * 
 * this call can be inherted from to create other session schemes. Such as DBSession
 * note that a session should be simi presistant between requests. Unlike caches.
 * 
 * Example
 * <code>
 * $registry->session->apples = "green";
 * 
 * $x = $registry->session->apples;
 * 
 * $x == "green" // true
 * 
 * 
 * </code>
 * @package DCore/core
 */
class session extends baseClass {

    function __construct($registry) {
        session_start();
        parent::__construct($registry);
    }

    /**
     * defualt module init() 
     */
    function init() {
        
    }

    /**
     * is a standard setter that set values to $_SESSION
     *
     * @param type $index
     * @param type $value 
     */
    public function __set($index, $value) {
        $_SESSION[$index] = $value;
    }

    /** the getter of session value
     *
     * @param type $index
     * @return null 
     */
    public function __get($index) {

        if (isset($_SESSION[$index]))
            return $_SESSION[$index];
        return null;
    }

}

