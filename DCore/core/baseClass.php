<?php

/**
 * Most classes inherit from baseClass 
 *
 * base classes main purpose is to hold a pointer to the registry and options for that 
 * class
 * 
 * @version $Id$
 * @copyright 2009
 * @package DCore/core
 */
class baseClass {

    /**
     * @var  Registry 
     */
    public $registry;
    public $options;
    /**
     * modules and plugins are assumed to have a init() function. They are usually
     * called after all others are loaded
     * 
     */
    public function init() {
        
    }
    /** standard construct or that should be called by inherited calsses
     *
     * @param registry $registry
     * @param mixed $options 
     */
    function __construct($registry, $options = null) {
        $this->registry = $registry;
        $this->options = $options;
    }

}

