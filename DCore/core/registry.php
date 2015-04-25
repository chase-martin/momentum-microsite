<?php

/**
 * Regsitry is a global call that is used to hold all framework varables such as modules.
 * 
 * 
 * @package DCore/core 
 */
Class Registry {
    /*
     * @the vars array
     * @access private
     */

    private $vars = array();

    /**
     * Magic properties that are usually connected to registry. To help code complete in most IDE
     * @property template $template
     * @property router $router
     */

    /**
     * Typical __set setter for assigning any value to registry
     *
     * @set undefined vars
     *
     * @param string $index
     *
     * @param mixed $value
     *
     * @return void
     *
     */
    public function __set($index, $value) {
        $this->vars[$index] = $value;
    }

    /**
     * A typical getter for getting values set with __set
     * @get variables
     *
     * @param mixed $index
     *
     * @return mixed
     *
     */
    public function __get($index) {
        if (isset($this->vars[$index]))
        return $this->vars[$index];
        return null;
    }

    /**
     * Loads all the modules in $CONFIG['modules']
     * 
     * the elements in the array of $CONFIG['modules'] each the modules configuration proerties
     * 
     * <code>
     * array( 'class' => "" , //string class name. if empty the then use key
     *        'alias' => "" , // the name used to name the value set in regisrty
     *        'option' => array() , // an array sent to the module to set module specific params
     *       )
     * </code>
     * 
     * will create each module in order and call the modules ->init() function
     * 
     * before returning call  <code> __PROTECTED_PATH . 'init.php';</code> if file exists
     * 
     * @global array[] $CONFIG['modules'] 
     */
    public function load() {
        global $CONFIG;

        $mods = $CONFIG['modules'];
        foreach ($mods as $key => $item) {

            if (!is_array($item)) {
                $key = $item;
            }
            $cf = array('class' => $key, 'alias' => $key);
            if (is_array($item)) {
                $cf = array_merge($cf, $item);
            }

            $alias = $cf['alias'];
            $class = $cf['class'];


            $options = $cf['options'];

            $class = DCore::loadClass($class);
            $this->$alias = new $class($this, $options);
            $this->$alias->init();
        }
        /// load the project init.php file after all modules loaded
        if (file_exists(__PROTECTED_PATH . 'init.php'))
            include __PROTECTED_PATH . 'init.php';
    }

}
