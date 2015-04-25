<?php

$CONFIG = merge_config(array('plugins' => array()), $CONFIG);

/**
 * A plugin can either use a class to load or an init.php
 * 
 * if a init.php file is used a generic plugin class is created and loads the init.php
 * if a plugin uses a plugin class instead it must over ride init()  
 * 
 * 
 * @package DCore/core
 */
class plugin extends baseClass {

    public $path = '';
    public $name = '';

    function __construct($plugin, $path, $registry, $options = null) {
        parent::__construct($registry, $options);
        $this->path = rtrim($path, '/');
        $this->name = $plugin;
    }

    function useDefault() {
        $class = get_class($this);
        $controller = str_replace('Plugin', '', $class);
        $this->registry->router->setController($controller, $controller . ':' . $controller);
    }

    function init() {
        $registry = $this->registry;
        $plugin = $this;
        include( rtrim($this->path, '/') . '/init.php');
    }

}

/**
 *
 * @package DCore/core 
 */
class plugins extends baseClass {

    private $_pluginPaths = array();
    private $_loadedPlugins = array();

    function init() {
        $this->loadPlugins();
    }

    function _createPluginClass($plugin, $plugpath) {
        $pluginclass = 'plugin';
        if (file_exists(rtrim($plugpath) . '/' . $plugin . 'Plugin.php')) {

            require_once( rtrim($plugpath) . '/' . $plugin . 'Plugin.php');
            $pluginclass = $plugin . 'Plugin';
        }
        $p = new $pluginclass($plugin, $plugpath, $this->registry);

        return $p;
    }

    function loadPlugin($plugin) {
        global $CONFIG, $registry;

        foreach ($this->pluginPath as $path) {

            $plugpath = $path . $plugin;
            $init = $plugpath . '/init.php';
            $pl = $plugpath . '/' . $plugin . 'Plugin.php';
            if (file_exists($init) or (file_exists($pl))) {
                DCore::setPathOfAlias($plugin, $plugpath);
                $pluginclass = $this->_createPluginClass($plugin, $plugpath);
                $pluginclass->init();
                $this->_loadedPlugins[$plugin] = $pluginclass;
                return true;
            }
        }
        die('plugin ' . $plugin . ' not found');
    }

    function addPluginDirectory($name, $path) {
        $this->_pluginPaths[$name] = $path;
    }

    function requiredPlugins($plugs) {
        foreach ($plugs as $plug) {
            if (empty($this->_loadedPlugins[$plug])) {
                echo('plugin ' . $plug->name . ' required');
                var_dump($plugs);
                echo('loaded plugins');
                var_dump($this->_loadedPlugins);   
                die();
            }
        }
    }

    function loadPlugins() {
        global $CONFIG;
        if (empty($this->pluginPath))
            $this->pluginPath = $CONFIG['pluginPath'];
        if (empty($this->pluginPath)) {
            $this->pluginPath[] = __PROTECTED_PATH . 'plugins/';
            $this->pluginPath[] = __FRAMEWORK_PATH . 'core/';
        }

        $plugins = $CONFIG['plugins'];
        foreach ($plugins as $plugin) {
            $this->loadPlugin($plugin);
        }
    }

}
