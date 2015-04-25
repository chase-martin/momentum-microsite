<?php

/**
 *
 * @package DCore/core
 */
define('DIRECTORY_SEPARATOR', '/');
require_once __FRAMEWORK_PATH . 'baseClass.php';
require_once __FRAMEWORK_PATH . 'controller_base.php';

/**
  }
 * Autoload for classes
 * @global __autoload  Enums $searchPaths to find a matching filename and includes it
 * @param string $class_name classname
 * @return boolean
 * @package DCore/core
 */
function DCoreAutoload($class_name) {
    $file = can_auto_load($class_name);
    if (file_exists($file)) {
        require_once ($file);
        return true;
    }

    return false;
}

spl_autoload_register('DCoreAutoload');

/**
 * checks if the class can be loaded.
 *
 *
 * @global type $CONFIG
 * @param type $class_name
 * @return boolean|string
 */
function can_auto_load($class_name) {
    global $CONFIG;
    //create filename from classname

    $class_name = str_replace("xhp_", "/", $class_name);
    $class_name = str_replace('__', '/', $class_name);

    $filename = $class_name . '.class.php';
    $filename2 = $class_name . '.php';

    // emum   $searchPaths
    foreach ($CONFIG['searchPaths'] as $path) {
        
        
       $file = str_replace(array('//','\\'), '/', $path.'/'. $filename);
          //include class source file if found
        if (DCORE::file_exists($file)) {
            return ($file);
        }
        $file = str_replace(array('//','\\'), '/', $path.'/'.  $filename2);
        
        //include class source file if found
        if (DCORE::file_exists($file)) {
            return ($file);
        } 
    }
    return false;
}

function merge_config_item($core, $config) {
    if (empty($core))
        return $config;
    if (empty($config))
        return $core;
    return array_merge($core, $config);
}

function merge_config($core, $config) {
    foreach ($core as $key => $item) {

        $config[$key] = merge_config_item($core[$key], $config[$key]);
    }
    return $config;
}

