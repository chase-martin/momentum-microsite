<?php

/**
 *this file is used to add and set up need framework classes and varibles 
 *  
 * @package DCore
 */

// Define frameworks main paths
define('__FRAMEWORK_PATH', dirname(__FILE__) . '/core/');
define('__DCORE', dirname(__FILE__) . '/');
define('__ROOT_PATH', dirname(__FILE__) . '/');


// ensure that the root path is define/
// root path is the path that index.php is locationed and usually protect is in.
if (!defined('__ROOT_PATH'))
    die('need define const "__ROOT_PATH"');
if (!defined('__PROTECTED_PATH'))
    define('__PROTECTED_PATH', __ROOT_PATH . '/protected/');
if (!defined('__TEMP_PATH'))
    define('__TEMP_PATH', __ROOT_PATH . '/protected/runtime/temp/');

// include the static singlton DCORE
require_once dirname(__FILE__) . '/DCore.php';


// these are the only two global varables 
global $registry, $CONFIG;

// include application configuration file
include __PROTECTED_PATH . '/config.php';


// add standard aliases
DCore::setPathOfAlias('lib', __FRAMEWORK_PATH);
DCore::setPathOfAlias('DCORE', dirname(__FILE__) . '/');
DCore::setPathOfAlias('runtime', __PROTECTED_PATH . '/runtime/');
DCore::setPathOfAlias('app', __PROTECTED_PATH . '/');
DCore::setPathOfAlias('root', __ROOT_PATH . '/');

// base functions
require_once __FRAMEWORK_PATH . 'base.php';


// A defualt configuration to be merged with the application configuration
// the application does not overwrite but merges.
$CONFIG_DEFAULT = array(
    'urls' => array('URL_ROOT' => '/'),
    'paths' => array('__FRAMEWORK_PATH' => __FRAMEWORK_PATH),
    'searchPaths' => array(__FRAMEWORK_PATH, __DCORE),
    'modules' => array(
    )
);

$CONFIG = merge_config($CONFIG_DEFAULT, $CONFIG);


// define standard URLS
define('URL_ROOT', $CONFIG['urls']['URL_ROOT']);
define('URL_THEME', URL_ROOT . 'theme/');



// creeate the global registry object. 
$registry = new registry;

